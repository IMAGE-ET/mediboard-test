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
$mediusers = new CMediusers();
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChirs = $mediusers->loadPraticiens(PERM_EDIT);
} else {
  $listChirs = $mediusers->loadProfessionnelDeSante(PERM_EDIT);
}

/// TODO : Simplifer tout ça pour tirer partie au mieux de inc_vw_week

$listDays = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $debut);
  $listDays[$dateArr] = $dateArr;   
}

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

// Création du tableau de visualisation
$affichages = array();

foreach ($listDays as $keyDate=>$valDate){
  foreach (CPlageconsult::$hours as $keyHours=>$valHours){
    foreach (CPlageconsult::$minutes as $keyMins=>$valMins){
      // Initialisation du tableau
      $affichages["$keyDate $valHours:$valMins:00"] = "empty";
    }
  }
}
	
$listPlages = array();
 
// Variable permettant de compter les jours pour la suppression du samedi et du dimanche
$i = 0;

// Détermination des bornes du semainier
$min = CPlageconsult::$hours_start.":".reset(CPlageconsult::$minutes).":00";
$max = CPlageconsult::$hours_stop.":".end(CPlageconsult::$minutes).":00";

$sans_plage_samedi   = "";
$sans_plage_dimanche = "";

// Extraction des plagesconsult par date
foreach($listDays as $keyDate=>$valDate){
  
  // Récupération des plages par jour
  $listPlage = new CPlageConsult();
  $where = array();
  $where["date"] = "= '$keyDate'";
  $where["chir_id"] = " = '$chirSel'";
  $order = "debut";
  $listPlages[$keyDate] = $listPlage->loadList($where,$order);
  
  // suppression des jours sans plage de consult (Samedi et dimanche)
  if (!$listPlages[$keyDate] && $i == 5) {
    $sans_plage_samedi = $keyDate;
  }
  else if(!$listPlages[$keyDate] && $i == 6){
    $sans_plage_dimanche = $keyDate;
  }
  
  $i++;
 
  // Détermination des bornes de chaque plage
  foreach($listPlages[$keyDate] as $plage){
    $plage->loadRefsBack();
    $plage->countPatients();
    $plage->debut = mbTimeGetNearestMinsWithInterval($plage->debut, CPlageconsult::$minutes_interval);
    $plage->fin   = mbTimeGetNearestMinsWithInterval($plage->fin  , CPlageconsult::$minutes_interval);
    // Si la plage se finit à 23h59, il faut y rester sinon on passe au lendemain.
    if ($plage->fin == "24:00:00") {
      $plage->fin = "23:59:59";
    }
    $min = $min > $plage->debut ? $plage->debut : $min;
    $max = $max < $plage->fin ? $plage->fin : $max;
    $plage->updateFormFields();
    
    if($plage->debut >= $plage->fin){
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }

  foreach($listPlages[$keyDate] as $plage){
    $plage->_nb_intervals = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageconsult::$minutes_interval.":00");
    $j = 0;
    $affichages["$keyDate $plage->debut"] = $plage->_id;
  }
}

if ($sans_plage_dimanche) {
  unset($listDays[$sans_plage_dimanche]);
  if ($sans_plage_samedi) {
    unset($listDays[$sans_plage_samedi]);
  }
}

// Extension du semainier s'il y a des plages qui d?passent des bornes
// de configuration hours_start et hours_stop
$hours = CPlageconsult::$hours;

$min_hour = sprintf("%01d", mbTransformTime($min, null, "%H"));
$max_hour = sprintf("%01d", mbTransformTime($max, null, "%H"));
$hours[$min_hour-1]=sprintf("%02d", $min_hour-1);
if (!isset($hours[$min_hour])) {
  for($i = $min_hour; $i < CPlageconsult::$hours_start; $i++) {
    $hours[$i] = sprintf("%02d", $i);
    $hours[$i-1] = sprintf("%02d", $i-1);
  }
}

if (!isset($hours[$max_hour])) {
  for($i = CPlageconsult::$hours_stop + 1; $i < $max_hour + 1; $i++) {
    $hours[$i] = sprintf("%02d", $i);
  }
}

ksort($hours);

//Planning au format  CPlanningWeek
$debut = CValue::getOrSession("debut", $today);
$debut = mbDate("-1 week", $debut);
$debut = mbDate("next monday", $debut);

//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, count($listDays), false, null, null, true);
if($mediusers->load($chirSel)){
  $planning->title = $mediusers->load($chirSel)->_view;
}
else{$planning->title = "";}
$planning->guid = $mediuser->_guid;
$planning->hour_min = "7";
$planning->hour_max = "20";
$planning->pauses = array("07", "12", "19");
$planning->hours = $hours;

$tab1 = array("class" => "button list notext", "href" => "#", "title" => "Voir le contenu de la plage" );
$tab2 = array("class" => "button edit notext", "href" => "#", "title" => "Modifier cette plage" );
$tab3 = array("class" => "button clock notext", "onclick" => "", "title" => "Planifier une consultation dans cette plage" );

//Ajout de tous les évènements
foreach($hours as $curr_hour){
  foreach(CPlageconsult::$minutes as $keyMins => $curr_mins){
    foreach($listDays as $curr_day){
    	$keyAff = "$curr_day $curr_hour:$curr_mins:00";
    	if(isset($affichages["$keyAff"])){
    	$affichage = $affichages["$keyAff"];
	    	$_listPlages = $listPlages["$curr_day"];
	    	if($_listPlages != null && $affichage != "empty"){
	    	$plage = $_listPlages["$affichage"];
	    	$titre = "";
	    	$guid = "";
	    	if($plage->libelle){
	        $titre = $plage->libelle;
	        $guid = $plage->_guid;
	    	}
	    	$debute = "$curr_day $plage->debut";
        //Création de l'évènement
	    	$event = new CPlanningEvent($guid, $debute, mbMinutesRelative($plage->debut, $plage->fin), $titre, "#CCC", true, null, null);
	    	//Menu des évènements
	    	$event->menu = true;
	    	$event->elements_menu["class"] = "toolbar";
	    	$event->elements_menu["elements"][0] = $tab1; 
	    	$event->elements_menu["elements"][0]["onclick"] = "showConsultations(this,'$plage->_id');"; 
	    	$event->elements_menu["elements"][1] = $tab2; 
	    	$event->elements_menu["elements"][1]["onclick"] = "PlageConsultation.edit('$plage->_id');"; 
	    	$event->elements_menu["elements"][2] = $tab3; 
	    	$event->elements_menu["elements"][2]["href"] = "?m=dPcabinet&tab=edit_planning&consultation_id=0&plageconsult_id=$plage->_id"; 
	    	//Paramètres de la plage de consultation
	    	$event->type = "consultation";
	    	$event->plage["id"] = $plage->plageconsult_id; 
	    	$pct = $plage->_fill_rate;
	    	if($pct>"100"){$pct="100";}
	    	$event->plage["pct"] = $pct;
	    	$event->plage["locked"] = $plage->locked;
	    	$event->plage["_affected"] = $plage->_affected;
	    	$event->plage["_nb_patients"] = $plage->_nb_patients;
	    	$event->plage["_total"] = $plage->_total;
	    	//Ajout de l'évènement au planning 
			  $planning->addEvent($event);
	    	}
	    }
    }  	
  }
}
foreach($planning->events as $_event){
	$_event->width = 0.98;
	$_event->offset = 0;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("planning"          , $planning);  
$smarty->assign("plageconsult_id"   , $plageconsult_id);
$smarty->assign("hide_payees"       , $hide_payees);
$smarty->assign("hide_annulees"     , $hide_annulees);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("listChirs"         , $listChirs);
$smarty->assign("today"             , $today);
$smarty->assign("debut"             , $debut);
$smarty->assign("fin"               , $fin);
$smarty->assign("prec"              , $prec);
$smarty->assign("suiv"              , $suiv);
$smarty->assign("count_si_desistement", $count_si_desistement);
$smarty->assign("bank_holidays"     , mbBankHolidays($today));

$smarty->display("vw_planning.tpl");
?>