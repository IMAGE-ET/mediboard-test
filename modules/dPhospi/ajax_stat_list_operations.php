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

$date       = CValue::get("date");
$service_id = CValue::get("service_id");
$group_id   = CGroups::loadCurrent()->_id;

$where = array();
$ljoin = array();

$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where["duree_uscpo"] = "> 0";
$where["annulee"] = "!= '1'";

$where[] = "(operations.date <= '$date' OR plagesop.date <= '$date') AND
  (DATE_ADD(plagesop.date, INTERVAL duree_uscpo DAY) > '$date' OR
   DATE_ADD(operations.date, INTERVAL duree_uscpo DAY) = '$date')";

$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where["sejour.group_id"] = "= '$group_id'";

if ($service_id) {  
  $where["sejour.service_id"] = " = '$service_id'";
}

// Prévues
$operation = new COperation();
$operations_prevues = $operation->loadList($where, null, null, null, $ljoin);

// Placées
$ljoin["affectation"] = "affectation.sejour_id = operations.sejour_id";
$where[] = "DATE_ADD(plagesop.date, INTERVAL duree_uscpo DAY) <= affectation.sortie";
$operations_placees = $operation->loadList($where, null, null, null, $ljoin);

CMbObject::massLoadFwdRef($operations_prevues, "plageop_id");
CMbObject::massLoadFwdRef($operations_prevues, "chir_id");
CMbObject::massLoadFwdRef($operations_placees, "plageop_id");
CMbObject::massLoadFwdRef($operations_placees, "chir_id");

foreach ($operations_prevues as $_operation) {
  $_operation->loadRefPatient();
  $_operation->loadRefPlageOp();
  $_operation->loadRefChir()->loadRefFunction();
}

foreach ($operations_placees as $_operation) {
  $_operation->loadRefPatient();
  $_operation->loadRefPlageOp();
  $_operation->loadRefChir()->loadRefFunction();
}

$smarty = new CSmartyDP();

$smarty->assign("operations_prevues", $operations_prevues);
$smarty->assign("operations_placees", $operations_placees);

$smarty->display("inc_stat_list_operations.tpl");

