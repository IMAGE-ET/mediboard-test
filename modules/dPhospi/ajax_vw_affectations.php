<?php /* $Id: ajax_vw_affectations.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm", "praticien");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filter_function = CValue::getOrSession("filter_function");
$date            = CValue::getOrSession("date");
$granularite     = CValue::getOrSession("granularite");
$readonly        = CValue::getOrSession("readonly", 0);

$heureLimit = "16:00:00";
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["annule"] = "= '0'";
$where["sejour.group_id"] = "= '$group_id'";
$where[] = "(sejour.type != 'seances' && affectation.affectation_id IS NULL) || sejour.type = 'seances'";

$order = null;
switch ($triAdm) {
  case "date_entree":
    $order = "entree_prevue ASC";
    break;
  case "praticien":
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
    break;
  case "patient" :
    $order = "patients.nom, patients.prenom";
    break;
}

switch ($_type_admission) {
  case "ambucomp":
    $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp'";
    break;
  case "0":
    break;
  default:
    $where["sejour.type"] = "= '$_type_admission'"; 
}

$sejour = new CSejour;
$ljoin = array(
  "affectation"     => "sejour.sejour_id = affectation.sejour_id",
  "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  "patients"        => "sejour.patient_id = patients.patient_id"
);

$period = "";
$nb_unite = 0;

switch ($granularite) {
  case "day":
    $period = "1hour";
    $unite = "hour";
    $nb_unite = 1;
    $nb_ticks = 24;
    $date_min = mbDateTime($date);
    break;
  case "week":
  	$period = "6hours";
    $unite = "hour";
    $nb_unite = 6;
    $nb_ticks = 28;
    $date_min = mbDateTime("-2 days", $date);
    break;
  case "4weeks":
  	$period = "1day";
    $unite = "day";
    $nb_unite = 1;
    $nb_ticks = 28;
    $date_min = mbDateTime("-1 week", CMbDate::dirac("week", $date));
}

$offset = $nb_ticks * $nb_unite;
$date_max = mbDateTime("+ $offset $unite", $date_min);

$temp_datetime = mbDateTime(null, $date_min);

for ($i = 0 ; $i < $nb_ticks ; $i++) {
  $offset = $i * $nb_unite;
  
  $datetime = mbDateTime("+ $offset $unite", $date_min);
  $datetimes[] = $datetime;
  if ($granularite == "4weeks") {
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
     // Dans la même semaine
     else {
       $change_month[$week_b] = array("left" => $temp_datetime, "right" => $datetime);
     }
   }
  }
  else {
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

$where["sejour.entree"] = "<= '$date_max'";
$where["sejour.sortie"] = ">= '$date_min'";

$sejours_non_affectes = $sejour->loadList($where, $order, null, null, $ljoin);

$praticiens = CMbObject::massLoadFwdRef($sejours_non_affectes, "praticien_id");
CMbObject::massLoadFwdRef($sejours_non_affectes, "patient_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");

$functions_filter = array();
$operations = array();

foreach($sejours_non_affectes as $_key => $_sejour) {
  $_sejour->loadRefPraticien()->loadRefFunction();
  $functions_filter[$_sejour->_ref_praticien->function_id] = $_sejour->_ref_praticien->_ref_function;
  if ($filter_function && $filter_function != $_sejour->_ref_praticien->function_id) {
    unset($sejours_non_affectes[$_key]);
  }
  else {
    $_sejour->_entree_offset = CMbDate::position(max($date_min, $_sejour->entree), $date_min, $period);
    $_sejour->_sortie_offset = CMbDate::position(min($date_max, $_sejour->sortie), $date_min, $period);
    $_sejour->_width = $_sejour->_sortie_offset - $_sejour->_entree_offset;
    $_sejour->loadRefPatient()->loadRefPhotoIdentite();
  }
  
  if (isset($operations[$_sejour->_id])) {
    $_operations = $operations[$_sejour->_id];
  }
  else {
    $operations[$_sejour->_id] = $_operations = $_sejour->loadRefsOperations();
  }
  
  foreach ($_operations as $key=>$_operation) {
    $_operation->loadRefPlageOp(1);
    
    if (($_operation->_datetime < $date_min) || ($_operation->_datetime > $date_max)) {
      unset($_sejour->_ref_operations[$key]);
      continue;
    }
    
    $hour_operation = mbTransformTime(null, $_operation->temp_operation, "%H");
    $min_operation = mbTransformTime(null, $_operation->temp_operation, "%M");
    $_operation->_debut_offset = CMbDate::position($_operation->_datetime, max($date_min, $_sejour->entree), $period);
    $_operation->_fin_offset = CMbDate::position(mbDateTime("+$hour_operation hours +$min_operation minutes",$_operation->_datetime), max($date_min, $_sejour->entree), $period);
    $_operation->_width = $_operation->_fin_offset - $_operation->_debut_offset;
  }
}

$sejour = new CSejour;
$sejour->_type_admission = $_type_admission;

$smarty = new CSmartyDP;

$smarty->assign("sejours_non_affectes", $sejours_non_affectes);
$smarty->assign("sejour", $sejour);
$smarty->assign("triAdm", $triAdm);
$smarty->assign("functions_filter", $functions_filter);
$smarty->assign("filter_function", $filter_function);
$smarty->assign("granularite", $granularite);
$smarty->assign("date"     , $date);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("nb_ticks", $nb_ticks);
$smarty->assign("days"    , $days);
$smarty->assign("datetimes", $datetimes);
$smarty->assign("readonly", $readonly);
$smarty->display("inc_vw_affectations.tpl");
?>