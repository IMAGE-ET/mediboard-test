<?php

/**
 * dPbloc
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$date_depart = CValue::get("date_depart");
$bloc_id     = CValue::get("bloc_id");
$order_way   = CValue::get("order_way");
$order_col   = CValue::get("order_col");

$operation = new COperation;

$ljoin = array();
$where = array();

$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where["plagesop.date"] = " = '$date_depart'";

if ($bloc_id) {
  $ljoin["sallesbloc"] = "sallesbloc.salle_id = operations.salle_id";
  $where["sallesbloc.bloc_id"] = "= '$bloc_id'";
}

// Avec plages
$operations = $operation->loadList($where, "time_operation asc", null, null, $ljoin);

// Hors plages
$where = array();
$ljoin = array();

$where["plageop_id"] = "IS NULL";
$where["date"] = " = '$date_depart'";

if ($bloc_id) {
  $ljoin["sallesbloc"] = "sallesbloc.salle_id = operations.salle_id";
  $where["sallesbloc.bloc_id"] = "= '$bloc_id'";
}
/** @var COperation[] $operations */
$operations = array_merge($operation->loadList($where, null, null, null, $ljoin), $operations);

$sejours = CMbObject::massLoadFwdRef($operations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($operations, "salle_id");
CMbObject::massLoadFwdRef($operations, "plageop_id");

foreach ($operations as $_operation) {
  $_operation->loadRefPlageOp();
  $_operation->updateSalle();
  $_operation->updateHeureUS();
  $sejour = $_operation->loadRefSejour();
  $affectation = $sejour->loadRefCurrAffectation($date_depart);
  $affectation->loadView();
  $sejour->loadRefPatient();
}

// Tri à posteriori
switch ($order_col) {
  case "nom" :
    $sorter = CMbArray::pluck($operations, "_ref_sejour", "_ref_patient", "nom");
    break;
  case "time_operation":
    $sorter = CMbArray::pluck($operations, "time_operation");
    break;
  case "salle_id":
    $sorter = CMbArray::pluck($operations, "salle_id");
    break;
  case "_heure_us":
  default :
    $sorter = CMbArray::pluck($operations, "_heure_us");
}

array_multisort($sorter, $order_way == "ASC" ? SORT_ASC : SORT_DESC, $operations);
$smarty = new CSmartyDP;

$smarty->assign("operations", $operations);
$smarty->assign("order_way" , $order_way);
$smarty->assign("order_col" , $order_col);

$smarty->display("inc_vw_departs_us.tpl");

