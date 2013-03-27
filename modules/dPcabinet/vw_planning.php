<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
CCanDo::checkRead();

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$show_payees   = CValue::getOrSession("show_payees"  , 1);
$show_annulees = CValue::getOrSession("show_annulees", 0);

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Période
$today         = CMbDT::date();
$debut         = CValue::getOrSession("debut", $today);
$debut         = CMbDT::date("last sunday", $debut);
$fin           = CMbDT::date("next sunday", $debut);
$debut         = CMbDT::date("+1 day", $debut);
$bank_holidays = array_merge(CMbDT::bankHolidays($debut), CMbDT::bankHolidays($fin));

$is_in_period = ($today >= $debut) && ($today <= $fin);

$prec = CMbDT::date("-1 week", $debut);
$suiv = CMbDT::date("+1 week", $debut);

// Plage de consultation selectionnée
$plageconsult_id = CValue::getOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
if (($plageconsult_id === null) && $chirSel && $is_in_period) {
  $nowTime = CMbDT::time();
  $where = array(
    "chir_id" => "= '$chirSel'",
    "date"    => "= '$today'",
    "debut"   => "<= '$nowTime'",
    "fin"     => ">= '$nowTime'"
  );
  $plageSel->loadObject($where);
}
if (!$plageSel->plageconsult_id) {
  $plageSel->load($plageconsult_id);
}
else {
  $plageconsult_id = $plageSel->plageconsult_id;
}

if ($plageSel->chir_id != $chirSel && $plageSel->remplacant_id != $chirSel) {
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
$now = CMbDT::date();
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

$dateArr = CMbDT::date("+6 day", $debut);

$listPlage = new CPlageconsult();

$where = array();
$where["date"] = "= '$dateArr'";
$where["chir_id"] = " = '$chirSel'";

if (!$listPlage->countList($where)) {
  $nbjours--;
  // Aucune plage le dimanche, on peut donc tester le samedi.
  $dateArr = CMbDT::date("+5 day", $debut);
  $where["date"] = "= '$dateArr'"; 
  if (!$listPlage->countList($where)) {
    $nbjours--;
  }
}

$hours = CPlageconsult::$hours;

//Planning au format  CPlanningWeek
$debut = CValue::getOrSession("debut", $today);
$debut = CMbDT::date("-1 week", $debut);
$debut = CMbDT::date("next monday", $debut);

//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, $nbjours, false, 450, null, true);
if ($user->load($chirSel)) {
  $planning->title = $user->load($chirSel)->_view;
}
else {
  $planning->title = "";
}
$planning->guid = $mediuser->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses = array("07", "12", "19");

$plage = new CPlageconsult();

$where = array();
$where[] = "chir_id = '$chirSel' OR remplacant_id = '$chirSel' OR pour_compte_id = '$chirSel'";

for ($i = 0; $i < 7; $i++) {
  $jour = CMbDT::date("+$i day", $debut);
  $where["date"] = "= '$jour'";
  foreach ($plage->loadList($where) as $_plage) {
    $_plage->loadRefsBack();
    $_plage->countPatients();
    $debute = "$jour $_plage->debut";
    $libelle = "";
    if (CMbDT::minutesRelative($_plage->debut, $_plage->fin) >= 30 ) {
      $libelle = $_plage->libelle;
    }
    
    $color = "#DDD";
    if ($_plage->desistee) {
      if (!$_plage->remplacant_id) {
        $color = "#CCC";
      }
      elseif ($_plage->remplacant_id && $_plage->remplacant_id != $chirSel) {
        $color = "#FAA";
      }
      elseif ($_plage->remplacant_id && !$_plage->remplacant_ok) {
        $color = "#FDA";
      }
      elseif ($_plage->remplacant_id && $_plage->remplacant_ok) {
        $color = "#BFB";
      }
    }
    elseif ($_plage->pour_compte_id) {
      $color = "#EDC";
    }
    $event = new CPlanningEvent($_plage->_guid, $debute, CMbDT::minutesRelative($_plage->debut, $_plage->fin), $libelle, $color, true, null, null);

    //Menu des évènements
    $event->addMenuItem("list", "Voir le contenu de la plage");
    if ((!$_plage->remplacant_id || $_plage->remplacant_id != $chirSel) && 
        (!$_plage->pour_compte_id || $_plage->pour_compte_id != $chirSel)) {
      $event->addMenuItem("edit", "Modifier cette plage");
    }
    $event->addMenuItem("clock", "Planifier une consultation dans cette plage");

    //Paramètres de la plage de consultation
    $event->type = "consultation";
    $event->plage["id"] = $_plage->plageconsult_id;
    
    $pct = $_plage->_fill_rate;
    if ($pct > "100") {
      $pct = "100";
    }
    if ($pct == "") {
      $pct = 0;
    }
    
    $event->plage["pct"] = $pct;
    $event->plage["locked"] = $_plage->locked;
    $event->plage["_affected"] = $_plage->_affected;
    $event->plage["_nb_patients"] = $_plage->_nb_patients;
    $event->plage["_total"] = $_plage->_total;
    $event->plage["color"] = $_plage->color;

    //Ajout de l'évènement au planning 
    $planning->addEvent($event);
  }    
}

$planning->rearrange();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("planning"            , $planning);
$smarty->assign("show_payees"         , $show_payees);
$smarty->assign("show_annulees"       , $show_annulees);
$smarty->assign("chirSel"             , $chirSel);
$smarty->assign("plageSel"            , $plageSel);
$smarty->assign("listChirs"           , $listChir);
$smarty->assign("today"               , $today);
$smarty->assign("debut"               , $debut);
$smarty->assign("fin"                 , $fin);
$smarty->assign("prec"                , $prec);
$smarty->assign("suiv"                , $suiv);
$smarty->assign("plageconsult_id"     , $plageconsult_id);
$smarty->assign("count_si_desistement", $count_si_desistement);
$smarty->assign("bank_holidays"       , $bank_holidays);
$smarty->assign("mediuser"            , $mediuser);

$smarty->display("vw_planning.tpl");
