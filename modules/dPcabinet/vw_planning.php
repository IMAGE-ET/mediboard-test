<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$_firstconsult_time  = null;
$_lastconsult_time   = null;

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
  $_firstconsult_time = substr($firstconsult->heure, 0, 5);
  $lastconsult = end($plageSel->_ref_consultations);
  $_lastconsult_time  = substr($lastconsult->heure, 0, 5);
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

$listDays = array();
$listDaysSelect = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $debut);
  $listDays[$dateArr] = $dateArr;
  $listDaysSelect[$dateArr] = $dateArr;    
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
  if(!$listPlages[$keyDate] && ($i == 5 || $i == 6)){
    unset($listDays[$keyDate]);
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
    for($time = $plage->debut; $time <= $plage->fin; $time = mbTime("+".CPlageconsult::$minutes_interval." minutes", $time) ){
      // Si la plage se finit à 23h59, alors on sort losque tous les créneaux sont passés.
      // time vaut alors 00:00:00, mais de la journée suivante.
      if ($j == $plage->_nb_intervals) {
        break;
      }
      $affichages["$keyDate $time"] = "full";
      $j++;
    } 
    $affichages["$keyDate $plage->debut"] = $plage->_id;
  }
}

// Recherche d'heure completement vides
foreach($listDays as $keyDate=>$valDate){
  foreach(CPlageconsult::$hours as $keyHours=>$valHours){
    $heure_vide = 1;
    foreach(CPlageconsult::$minutes as $kayMins=>$valMins){
      // Vérification données
      if(!is_string($affichages["$keyDate $valHours:$valMins:00"]) || (is_string($affichages["$keyDate $valHours:$valMins:00"]) && $affichages["$keyDate $valHours:$valMins:00"]!= "empty")){
        $heure_vide = 0;
      }
    }
    if($heure_vide==1){
      $first = "hours";
      foreach(CPlageconsult::$minutes as $kayMins=>$valMins){
        // Mémorisation heure vide
        $affichages["$keyDate $valHours:$valMins:00"] = $first;
        $first = "full";
      }
    }
  }
}

// Extension du semainier s'il y a des plages qui dépassent des bornes
// de configuration hours_start et hours_stop
$hours = CPlageconsult::$hours;

$min_hour = sprintf("%01d", mbTransformTime($min, null, "%H"));
$max_hour = sprintf("%01d", mbTransformTime($max, null, "%H"));

if (!isset($hours[$min_hour])) {
  for($i = $min_hour; $i < CPlageconsult::$hours_start; $i++) {
    $hours[$i] = sprintf("%02d", $i);
  }
}

if (!isset($hours[$max_hour])) {
  for($i = CPlageconsult::$hours_stop + 1; $i < $max_hour + 1; $i++) {
    $hours[$i] = sprintf("%02d", $i);
  }
}

ksort($hours);

foreach ($listDays as $keyDate=>$valDate){
  foreach ($hours as $keyHours=>$valHours){
    foreach (CPlageconsult::$minutes as $keyMins=>$valMins){
      if (!isset($affichages["$keyDate $valHours:$valMins:00"])) {
        $affichages["$keyDate $valHours:$valMins:00"] = "empty";
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affichages"        , $affichages);  
$smarty->assign("listPlages"        , $listPlages);
$smarty->assign("_firstconsult_time", $_firstconsult_time);
$smarty->assign("_lastconsult_time" , $_lastconsult_time);
$smarty->assign("plageconsult_id"   , $plageconsult_id);
$smarty->assign("hide_payees"       , $hide_payees);
$smarty->assign("hide_annulees"     , $hide_annulees);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("listChirs"         , $listChirs);
$smarty->assign("listDays"          , $listDays);
$smarty->assign("listDaysSelect"    , $listDaysSelect);
$smarty->assign("today"             , $today);
$smarty->assign("debut"             , $debut);
$smarty->assign("fin"               , $fin);
$smarty->assign("prec"              , $prec);
$smarty->assign("suiv"              , $suiv);
$smarty->assign("listHours"         , $hours);
$smarty->assign("listMins"          , CPlageconsult::$minutes);
$smarty->assign("nb_intervals_hour" , intval(60/CPlageconsult::$minutes_interval));
$smarty->assign("count_si_desistement", $count_si_desistement);

$smarty->display("vw_planning.tpl");

	
?>