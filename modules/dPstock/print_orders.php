<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$invoiced     = CValue::get("invoiced");
$not_invoiced = CValue::get("not-invoiced");
$date_min     = CValue::get("date_min");
$date_max     = CValue::get("date_max");

$where = array(
  "date_ordered" => "BETWEEN '$date_min' AND '$date_max'",
);

if ($invoiced xor $not_invoiced) {
  $where["bill_number"] = $invoiced ? "IS NOT NULL" : "IS NULL";
}

$order = new CProductOrder;
$orders = $order->search("received", null, null, $where);

$count = $order->_search_count;

foreach($orders as $_order) {
  //$_order->updateCounts();
  $_order->countRenewedItems();
  if ($_order->object_id) {
    $_order->loadTargetObject();
    $_order->_ref_object->loadRefsFwd();
  }
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("orders", $orders);
$smarty->assign("invoiced", $invoiced);
$smarty->assign("not_invoiced", $not_invoiced);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);

$smarty->display("print_orders.tpl");
