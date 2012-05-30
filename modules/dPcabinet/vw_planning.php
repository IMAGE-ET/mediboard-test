<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$hide_payees   = CValue::getOrSession("hide_payees"  , 0);
$hide_annulees = CValue::getOrSession("hide_annulees", 1);

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Période
$today = mbDate();
$debut = CValue::getOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$is_in_period = ($today >= $debut) && ($today <= $fin);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Plage de consultation selectionnée
$plageconsult_id = CValue::getOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
if(($plageconsult_id === null) && $chirSel && $is_in_period) {
  $nowTime = mbTime();
  $where = array(
    "chir_id" => "= '$chirSel'",
    "date"    => "= '$today'",
    "debut"   => "<= '$nowTime'",
    "fin"     => ">= '$nowTime'"
  );
  $plageSel->loadObject($where);
}
if(!$plageSel->plageconsult_id) {
  $plageSel->load($plageconsult_id);
} else {
  $plageconsult_id = $plageSel->plageconsult_id;
}
$plageSel->loadRefsFwd(1);
$plageSel->loadRefsNotes();
$plageSel->loadRefsBack();

if ($plageSel->_affected) {
  $firstconsult = reset($plageSel->_ref_consultations);
  $lastconsult = end($plageSel->_ref_consultations);
}
// Détails sur les consultation affichées
foreach ($plageSel->_ref_consultations as $keyConsult => &$consultation) {
  // Cache les payées
  if ($hide_payees && $consultation->patient_date_reglement) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  // Cache les annulées
  if ($hide_annulees && $consultation->annule) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  $consultation->loadRefSejour(1);
  $consultation->loadRefPatient(1);
  $consultation->loadRefCategorie(1);
  $consultation->countDocItems();    
}
if ($plageSel->chir_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

CValue::setSession("plageconsult_id", $plageconsult_id);

// Liste des chirurgiens
$user = new CMediusers();
$listChir = CAppUI::pref("pratOnlyForConsult", 1) ?
  $user->loadPraticiens(PERM_EDIT) :
  $user->loadProfessionnelDeSante(PERM_EDIT);

  
// Liste des consultations a avancer si desistement
$now = mbDate();
$where = array(
  "plageconsult.date" => " > '$now'",
  "plageconsult.chir_id" => "= '$chirSel'",
  "consultation.si_desistement" => "= '1'",
);
$ljoin = array(
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);
$consultation_desist = new CConsultation;
$count_si_desistement = $consultation_desist->countList($where, null, $ljoin);
  
$nbjours = 7;

$dateArr = mbDate("+6 day", $debut);

$listPlage = new CPlageConsult();

$where = array();
$where["date"] = "= '$dateArr'";
$where["chir_id"] = " = '$chirSel'";

if (!$listPlage->countList($where)){
  $nbjours--;
  // Aucune plage le dimanche, on peut donc tester le samedi.
  $dateArr = mbDate("+5 day", $debut);
  $where["date"] = "= '$dateArr'"; 
  if (!$listPlage->countList($where)) {
    $nbjours--;
  }
}

$hours = CPlageconsult::$hours;

//Planning au format  CPlanningWeek
$debut = CValue::getOrSession("debut", $today);
$debut = mbDate("-1 week", $debut);
$debut = mbDate("next monday", $debut);

//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, $nbjours, false, 450, null, true);
if($user->load($chirSel)){
  $planning->title = $user->load($chirSel)->_view;
}
else{$planning->title = "";}
$planning->guid = $mediuser->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses = array("07", "12", "19");

$plage = new CPlageConsult();

$where = array();
$where["chir_id"] = " = '$chirSel'";

for ($i = 0; $i < 7; $i++) {
  $jour = mbDate("+$i day", $debut);
  $where["date"] = "= '$jour'";
  foreach($plage->loadList($where) as $_plage){
    $_plage->loadRefsBack();
    $_plage->countPatients();
    $debute = "$jour $_plage->debut";
    
    $libelle = "";
    if(mbMinutesRelative($_plage->debut, $_plage->fin) > 60 ){
    	 $libelle = $_plage->libelle;
    }
    $event = new CPlanningEvent($_plage->_guid, $debute, mbMinutesRelative($_plage->debut, $_plage->fin), $libelle, "#$_plage->color", true, null, null);

    //Menu des évènements
    $event->addMenuItem("list", "Voir le contenu de la plage");
    $event->addMenuItem("edit", "Modifier cette plage");
    $event->addMenuItem("clock", "Planifier une consultation dans cette plage");

    //Paramètres de la plage de consultation
    $event->type = "consultation";
    $event->plage["id"] = $_plage->plageconsult_id; 
    
    $pct = $_plage->_fill_rate;
    if($pct > "100"){
      $pct = "100";
    }
    if($pct == ""){
      $pct = 0;
    }
    
    $event->plage["pct"] = $pct;
    $event->plage["locked"] = $_plage->locked;
    $event->plage["_affected"] = $_plage->_affected;
    $event->plage["_nb_patients"] = $_plage->_nb_patients;
    $event->plage["_total"] = $_plage->_total;

    //Ajout de l'évènement au planning 
    $planning->addEvent($event);
  }    
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("planning"          , $planning);
$smarty->assign("hide_payees"       , $hide_payees);
$smarty->assign("hide_annulees"     , $hide_annulees);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("listChirs"         , $listChir);
$smarty->assign("today"             , $today);
$smarty->assign("debut"             , $debut);
$smarty->assign("fin"               , $fin);
$smarty->assign("prec"              , $prec);
$smarty->assign("suiv"              , $suiv);
$smarty->assign("plageconsult_id"    , $plageconsult_id);
$smarty->assign("count_si_desistement", $count_si_desistement);
$smarty->assign("bank_holidays"     , mbBankHolidays($today));

$smarty->display("vw_planning.tpl");
?>