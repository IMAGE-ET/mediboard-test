<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

$can->needsRead();

$_firstconsult_time  = null;
$_lastconsult_time   = null;

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$vue = mbGetValueFromGetOrSession("vue1");

// Praticien selectionné
$chirSel = mbGetValueFromGetOrSession("chirSel", $chir ? $chir->user_id : null);

// Période
$today = mbDate();
$debut = mbGetValueFromGetOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$is_in_period = ($today >= $debut) && ($today <= $fin);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Plage de consultation selectionnée
$plageconsult_id = mbGetValueFromGetOrSession("plageconsult_id", null);
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
$plageSel->loadRefsBack();

if ($plageSel->_affected) {
  $firstconsult = reset($plageSel->_ref_consultations);
  $_firstconsult_time = substr($firstconsult->heure, 0, 5);
  $lastconsult = end($plageSel->_ref_consultations);
  $_lastconsult_time  = substr($lastconsult->heure, 0, 5);
}

// Détails sur les consultation affichées
foreach ($plageSel->_ref_consultations as $keyConsult => &$consultation) {
  if ($vue && $consultation->patient_date_reglement) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  $consultation->loadRefPatient(1);
  $consultation->loadRefCategorie(1);
  $consultation->countDocItems();    
}

if ($plageSel->chir_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

mbSetValueToSession("plageconsult_id", $plageconsult_id);

// Liste des chirurgiens
$mediusers = new CMediusers();
$listChirs = $mediusers->loadPraticiens(PERM_EDIT);



$listDays = array();
$listDaysSelect = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $debut);
  $listDays[$dateArr] = $dateArr;
  $listDaysSelect[$dateArr] = $dateArr;    
}



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
  
  // Détermination des bornes du semainier
  $min = CPlageconsult::$hours_start.":".reset(CPlageconsult::$minutes).":00";
  $max = CPlageconsult::$hours_stop.":".end(CPlageconsult::$minutes).":00";
  
 
  // Détermination des bornes de chaque plage
  foreach($listPlages[$keyDate] as $plage){
    $plage->loadRefsBack();
    $plage->countPatients();
    $plage->debut = mbTimeGetNearestMinsWithInterval($plage->debut, CPlageconsult::$minutes_interval);
    $plage->fin   = mbTimeGetNearestMinsWithInterval($plage->fin  , CPlageconsult::$minutes_interval);
    $plage->fin = min($plage->fin, $max);
    $plage->debut = max($plage->debut, $min);
    $plage->updateFormFields();
    if($plage->debut >= $plage->fin){
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  foreach($listPlages[$keyDate] as $plage){
    $plage->_nb_intervals = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageconsult::$minutes_interval.":00");
    for($time = $plage->debut; $time < $plage->fin; $time = mbTime("+".CPlageconsult::$minutes_interval." minutes", $time) ){
      $affichages["$keyDate $time"] = "full";
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




// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affichages"        , $affichages);  
$smarty->assign("listPlages"        , $listPlages);
$smarty->assign("_firstconsult_time", $_firstconsult_time);
$smarty->assign("_lastconsult_time" , $_lastconsult_time);
$smarty->assign("plageconsult_id"   , $plageconsult_id);
$smarty->assign("vue"               , $vue);
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
$smarty->assign("listHours"         , CPlageconsult::$hours);
$smarty->assign("listMins"          , CPlageconsult::$minutes);
$smarty->assign("nb_intervals_hour" , intval(60/CPlageconsult::$minutes_interval));

$smarty->display("vw_planning.tpl");

	
?>