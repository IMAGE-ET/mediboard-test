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
$product_reference = new CProductReference;

$filter->_patient_id = CValue::getOrSession("_patient_id");
$filter->product_id  = CValue::getOrSession("product_id");
$filter->septic      = CValue::getOrSession("septic", 0);
$lot                 = CValue::getOrSession("lot");
$product_reference->societe_id = CValue::getOrSession("societe_id");

$filter->loadRefPatient();
$filter->loadRefsFwd();

$start = intval(CValue::getOrSession("start", 0));
$ds = $filter->_spec->ds;

// Filter query
$where = array(
  "prescription_line_dmi.product_id" => "IS NOT NULL",
  "prescription_line_dmi.septic" => $ds->prepare("=%", $filter->septic),
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

if ($product_reference->societe_id) {
  $where["product_reference.societe_id"] = $ds->prepare("=%", $product_reference->societe_id);
}

$join = array(
  "product_order_item_reception" => "product_order_item_reception.order_item_reception_id = prescription_line_dmi.order_item_reception_id",
  "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
  "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
  "operations" => "operations.operation_id = prescription_line_dmi.operation_id",
  "sejour" => "sejour.sejour_id = operations.sejour_id",
);
$order = "prescription_line_dmi.date";

$lines =      $filter->loadList($where, $order, "$start,30", null, $join);
$line_count = $filter->countList($where, null, $join);

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
$smarty->assign("product_reference", $product_reference);
$smarty->display("vw_tracabilite.tpl");
