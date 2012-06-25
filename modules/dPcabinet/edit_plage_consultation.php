<?php /* $Id: edit_plage_consultation.php$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision
* @author SARL OpenXtrem
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

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Période
$today = mbDate();
$debut = CValue::getOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$is_in_period = ($today >= $debut) && ($today <= $fin);

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
  if ($consultation->patient_date_reglement) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  // Cache les annulées
  if ($consultation->annule) {
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

$listDaysSelect = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $debut);
  $listDaysSelect[$dateArr] = $dateArr;    
}

// Variable permettant de compter les jours pour la suppression du samedi et du dimanche
$i = 0;

// Détermination des bornes du semainier
$min = CPlageconsult::$hours_start.":".reset(CPlageconsult::$minutes).":00";
$max = CPlageconsult::$hours_stop.":".end(CPlageconsult::$minutes).":00";

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_firstconsult_time", $_firstconsult_time);
$smarty->assign("_lastconsult_time" , $_lastconsult_time);
$smarty->assign("plageconsult_id"   , $plageconsult_id);
$smarty->assign("user"              , CMediusers::get());
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("listChirs"         , $listChirs);
$smarty->assign("listDaysSelect"    , $listDaysSelect);
$smarty->assign("listHours"         , $hours);
$smarty->assign("listMins"          , CPlageconsult::$minutes);
$smarty->assign("nb_intervals_hour" , intval(60/CPlageconsult::$minutes_interval));

$smarty->display("edit_plage_consultation.tpl");
  
?>
