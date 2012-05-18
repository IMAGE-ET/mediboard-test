<?php

/**
 * dPhospi
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$date = CValue::get("date");
$service_id = CValue::get("service_id");

$where = array();
$ljoin = array();

$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where["duree_uscpo"] = "> 0";
$where[] = "operations.date = '$date' OR plagesop.date = '$date'";


if ($service_id) {
  $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
  $where["sejour.service_id"] = " = '$service_id'";
}

$operation = new COperation;
$operations = $operation->loadList($where, null, null, null, $ljoin);

CMbObject::massLoadFwdRef($operations, "plageop_id");
CMbObject::massLoadFwdRef($operations, "chir_id");

foreach ($operations as $_operation) {
  $_operation->loadRefPatient();
  $_operation->loadRefPlageOp();
  $_operation->loadRefChir()->loadRefFunction();
}

$smarty = new CSmartyDP;

$smarty->assign("operations", $operations);

$smarty->display("inc_stat_list_operations.tpl");

?>