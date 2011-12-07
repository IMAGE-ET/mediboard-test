<?php /* $Id: ajax_vw_affectations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm", "praticien");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filter_function = CValue::getOrSession("filter_function");
$date            = CValue::getOrSession("date");

$heureLimit = "16:00:00";
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["annule"] = "= '0'";
$where["sejour.group_id"] = "= '$group_id'";
$where[] = "(sejour.type != 'seances' && affectation.affectation_id IS NULL) || sejour.type = 'seances'";

switch ($triAdm) {
  case "date_entree":
    $order = "entree_prevue ASC";
    break;
  case "praticien":
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
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

// Admissions de la veille
$dayBefore = mbDate("-1 days", $date);
$where["sejour.entree"] = "BETWEEN '$dayBefore 00:00:00' AND '$date 01:59:59'";
$sejours_non_affectes["veille"] = $sejour->loadList($where, $order, null, null, $ljoin);

// Admissions du matin
$where["sejour.entree"] = "BETWEEN '$date 02:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'";
$sejours_non_affectes["matin"] = $sejour->loadList($where, $order, null, null, $ljoin);

// Admissions du soir
$where["sejour.entree"] = "BETWEEN '$date $heureLimit' AND '$date 23:59:59'";
$sejours_non_affectes["soir"] = $sejour->loadList($where, $order, null, null, $ljoin);

// Admissions antérieures
$twoDaysBefore = mbDate("-2 days", $date);
$where["sejour.entree"] = "<= '$twoDaysBefore 23:59:59'";
$where["sejour.sortie"] = ">= '$date 00:00:00'";
$sejours_non_affectes["avant"] = $sejour->loadList($where, $order, null, null, $ljoin);

foreach ($sejours_non_affectes as $_sejours_by_period) {
  $praticiens = CMbObject::massLoadFwdRef($_sejours_by_period, "praticien_id");
  CMbObject::massLoadFwdRef($_sejours_by_period, "patient_id");
  CMbObject::massLoadFwdRef($praticiens, "function_id");
  foreach ($_sejours_by_period as $_sejour) {
    $_sejour->loadRefPatient();
    $_sejour->loadRefPraticien()->loadRefFunction();
    $_sejour->getDroitsCMU();
    $_sejour->loadRefPrestation();
    $_sejour->loadNDA();
    $sejour->loadRefsOperations();
    foreach($sejour->_ref_operations as &$operation) {
      $operation->loadExtCodesCCAM();
    }
  }
}

$functions_filter = array();
foreach($sejours_non_affectes as $_keyGroup => $_group) {
  foreach($_group as $_key => $_sejour) {
    $functions_filter[$_sejour->_ref_praticien->function_id] = $_sejour->_ref_praticien->_ref_function;
    if ($filter_function && $filter_function != $_sejour->_ref_praticien->function_id) {
      unset($sejours_non_affectes[$_keyGroup][$_key]);
    }
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

$smarty->display("inc_vw_affectations.tpl");
?>