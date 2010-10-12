<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Filter
$filter = new CPrescriptionLineDMI;
$filter->_patient_id             = CValue::getOrSession("_patient_id");
$filter->product_id              = CValue::getOrSession("product_id");
$lot = CValue::getOrSession("lot");

$filter->loadRefPatient();
$filter->loadRefsFwd();

$start = intval(CValue::getOrSession("start", 0));
$ds = $filter->_spec->ds;

// Filter query
$where = array(
  "prescription_line_dmi.product_id" => "IS NOT NULL",
);

if ($filter->_patient_id) {
  $where["sejour.patient_id"] = $ds->prepare("=%", $filter->_patient_id);
}

if ($filter->product_id) {
  $where["prescription_line_dmi.product_id"] = $ds->prepare("=%", $filter->product_id);
}

if ($lot) {
  $where["product_order_item_reception.code"] = $ds->prepareLike("$lot%");
}

$join = array(
  "product_order_item_reception" => "product_order_item_reception.order_item_reception_id = prescription_line_dmi.order_item_reception_id",
  "operations" => "operations.operation_id = prescription_line_dmi.operation_id",
  "sejour" => "sejour.sejour_id = operations.sejour_id",
);
$order = "prescription_line_dmi.date";

$lines =      $filter->loadList($where, $order, "$start,30", null, $join);
$line_count = $filter->countList($where, $order, null, null, $join);

// Detail loading
foreach($lines as $_line) {
	$_line->loadRefOperation();
  $_line->_ref_operation->loadRefSejour();
  $_line->_ref_operation->loadRefsFwd();
  $_line->loadRefProductOrderItemReception();
  $_line->loadRefProduct();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter"    , $filter);
$smarty->assign("start"     , $start);
$smarty->assign("lines"     , $lines);
$smarty->assign("lot"       , $lot);
$smarty->assign("line_count", $line_count);
$smarty->display("vw_tracabilite.tpl");
