<?php /* $Id: ajax_refresh_mouvements.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$services_ids = CValue::getOrSession("services_ids", null);
$readonly     = CValue::get("readonly", 0);
$granularite  = CValue::getOrSession("granularite", "day");
$date         = CValue::getOrSession("date", mbDate());
$mode_vue_tempo = CValue::getOrSession("mode_vue_tempo", "classique");
$readonly     = CValue::getOrSession("readonly", 0);

if (!$services_ids) {
  $smarty = new CSmartyDP;
  $smarty->display("inc_no_services.tpl");
  CApp::rip();
}

$unite = "";
$period = "";
$datetimes = array();
$change_month = array();
$granularites = array("day", "week", "4weeks");

switch ($granularite) {
  case "day":
    $unite = "hour";
    $nb_unite = 1;
    $nb_ticks = 24;
    $step = "+1 hour";
    $period = "1hour";
    $date_min = mbDateTime($date);
    $date_before = mbDate("-1 day", $date);
    $date_after  = mbDate("+1 day", $date);
    break;
  case "week":
    $unite = "hour";
    $nb_unite = 6;
    $nb_ticks = 28;
    $step = "+6 hours";
    $period = "6hours";
    $date_min = mbDateTime("-2 days", $date);
    $date_before = mbDate("-1 week", $date);
    $date_after = mbDate("+1 week", $date);
    break;
  case "4weeks":
    $unite = "day";
    $nb_unite = 1;
    $nb_ticks = 28;
    $step = "+1 day";
    $period = "1day";
    $date_min = mbDateTime("-1 week", CMbDate::dirac("week", $date));
    $date_before = mbDate("-4 week", $date);
    $date_after = mbDate("+4 week", $date);
}

$current = CMbDate::dirac("hour", mbDateTime());
$offset = $nb_ticks * $nb_unite;

$date_max = mbDateTime("+ $offset $unite", $date_min);
$temp_datetime = mbDateTime(null, $date_min);

for ($i = 0 ; $i < $nb_ticks ; $i++) {
  $offset = $i * $nb_unite;
  
  $datetime = mbDateTime("+ $offset $unite", $date_min);
  $datetimes[] = $datetime;
  
  if ($granularite == "4weeks") {
    if (mbDate($current) == mbDate($temp_datetime) &&
      mbTime($current) >= mbTime($temp_datetime) && mbTime($current) > mbTime($datetime)) {
      $current = $temp_datetime;
    }
    $week_a = mbTransformTime($temp_datetime, null, "%W");
    $week_b = mbTransformTime($datetime, null, "%W");
    if ($week_a == "00") {
      $week_a = "52";
    }
    if ($week_b == "00") {
      $week_b = "52";
    }
    // les semaines
    $days[$datetime] = $week_b;
    
    // On stocke le changement de mois s'il advient
   if (mbTransformTime($datetime, null, "%m") != mbTransformTime($temp_datetime, null, "%m")) {
     
     // Entre deux semaines
     if ($i%7 == 0) {
       $change_month[$week_a] = array("right"=>$temp_datetime);
       $change_month[$week_b] = array("left"=>$datetime);
     }
     // Dans la m�me semaine
     else {
       $change_month[$week_b] = array("left" => $temp_datetime, "right" => $datetime);
     }
   }
  }
  else {
    if ($granularite == "week" && mbDate($current) == mbDate($temp_datetime) &&
        mbTime($datetime) >= mbTime($temp_datetime) && mbTime($current) <= mbTime($datetime)) {
      $current = $temp_datetime;
    }
    if ($granularite)
    // le datetime, pour avoir soit le jour soit l'heure
    $days[] = mbDate($datetime);
  }
  $temp_datetime = $datetime;
}

$days = array_unique($days);

// Cas de la semaine 00
if ($granularite == "4weeks" && count($days) == 5) {
  array_pop($days);
}

// Chargement des lits
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["chambre.service_id"] = CSQLDataSource::prepareIn($services_ids);
$where["service.group_id"] = " = '$group_id'";
$ljoin = array();
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
$lit = new CLit;

$lits     = $lit->loadList($where, null, null, null, $ljoin);
$chambres = CMbObject::massLoadFwdRef($lits, "chambre_id");
$services = CMbObject::massLoadFwdRef($chambres, "service_id");

foreach ($lits as $_lit) {
  $chambre = $_lit->loadRefChambre();
  $chambre->_ref_lits[$_lit->_id] = $_lit;
  $service = $chambre->loadRefService();
  $service->_ref_chambres[$chambre->_id] = $chambre;
}

array_multisort(CMbArray::pluck($services, "nom"), SORT_ASC, $services);

// Chargement des affectations
$where = array();
$where["lit_id"] = CSQLDataSource::prepareIn(array_keys($lits));
$where["entree"] = "<= '$date_max'";
$where["sortie"] = ">= '$date_min'";

$affectation = new CAffectation;
$nb_affectations = $affectation->countList($where);
if ($nb_affectations > CAppUI::conf("dPhospi max_affectations_view")) {
  $smarty = new CSmartyDP;
  $smarty->display("inc_vw_max_affectations.tpl");
  CApp::rip();
}

$affectations = $affectation->loadList($where);

$sejours  = CMbObject::massLoadFwdRef($affectations, "sejour_id");
$patients = CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");
$operations = array();

foreach ($affectations as $_affectation) {
  $_affectation->loadRefsAffectations();
  $sejour = $_affectation->loadRefSejour();
  $sejour->loadRefPraticien()->loadRefFunction();
  $sejour->loadRefPatient()->loadRefPhotoIdentite();
  $lits[$_affectation->lit_id]->_ref_affectations[$_affectation->_id] = $_affectation;
  $_affectation->_entree_offset = CMbDate::position(max($date_min, $_affectation->entree), $date_min, $period);
  $_affectation->_sortie_offset = CMbDate::position(min($date_max, $_affectation->sortie), $date_min, $period);
  $_affectation->_width = $_affectation->_sortie_offset - $_affectation->_entree_offset;
  
  if (isset($operations[$sejour->_id])) {
    $_operations = $operations[$sejour->_id];
  }
  else {
    $operations[$sejour->_id] = $_operations = $sejour->loadRefsOperations();
  }
  
  foreach ($_operations as $key=>$_operation) {
    $_operation->loadRefPlageOp(1);
    
    if (($_operation->_datetime < $date_min) || ($_operation->_datetime > $date_max)) {
      unset($sejour->_ref_operations[$key]);
      continue;
    }
    
    $hour_operation = mbTransformTime(null, $_operation->temp_operation, "%H");
    $min_operation = mbTransformTime(null, $_operation->temp_operation, "%M");
    
    $_operation->_debut_offset = CMbDate::position($_operation->_datetime, max($date_min, $_affectation->entree), $period);
    
    $_operation->_fin_offset = CMbDate::position(mbDateTime("+$hour_operation hours +$min_operation minutes",$_operation->_datetime), max($date_min, $_affectation->entree), $period);
    $_operation->_width = $_operation->_fin_offset - $_operation->_debut_offset;
  }
}

foreach ($lits as $_lit) {
  $intervals = array();
  if (isset($_lit->_ref_affectations) && count($_lit->_ref_affectations)) {
    foreach ($_lit->_ref_affectations as $_affectation) {
      $intervals[$_affectation->_id] = array(
        "lower" => $_affectation->entree,
        "upper" => $_affectation->sortie,
      );
    }
    $_lit->_lines = CMbRange::rearrange($intervals);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("services"    , $services);
$smarty->assign("affectations", $affectations);
$smarty->assign("date"        , $date);
$smarty->assign("date_min"    , $date_min);
$smarty->assign("date_max"    , $date_max);
$smarty->assign("date_before" , $date_before);
$smarty->assign("date_after"  , $date_after);
$smarty->assign("granularites", $granularites);
$smarty->assign("granularite" , $granularite);
$smarty->assign("nb_ticks"    , $nb_ticks);
$smarty->assign("datetimes"   , $datetimes);
$smarty->assign("days"        , $days);
$smarty->assign("change_month", $change_month);
$smarty->assign("mode_vue_tempo", $mode_vue_tempo);
$smarty->assign("readonly"    , $readonly);
$smarty->assign("nb_affectations", $nb_affectations);
$smarty->assign("readonly"    , $readonly);
$smarty->assign("current"     , $current);
$smarty->display("inc_vw_mouvements.tpl");

?>