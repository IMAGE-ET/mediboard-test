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
  "DATE(date_ordered) BETWEEN '$date_min' AND '$date_max'",
);

if ($invoiced xor $not_invoiced) {
  $where["bill_number"] = $invoiced ? "IS NOT NULL" : "IS NULL";
}

$order = new CProductOrder;
$orders = $order->search("received", null, null, $where);

$count = $order->_search_count;
$total = 0;
$total_ttc = 0;

foreach($orders as $_order) {
  $_order->countRenewedItems();
  
  foreach ($_order->_ref_order_items as $item) {
    $item->loadRefsReceptions();
    $rec = reset($item->_ref_receptions);
    $_order->_date_received = $rec ? $rec->date : null;
  }
  
  if ($_order->object_id) {
    $_object = $_order->loadTargetObject();
    $_object->loadRefSejour()->loadNDA();
    $_object->loadRefsFwd();
  }
  
  $total += $_order->_total;
  $total_ttc += $_order->_total_tva;
}

$order->_total = $total;
$order->_total_tva = $total_ttc;

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("orders", $orders);
$smarty->assign("order", $order);
$smarty->assign("invoiced", $invoiced);
$smarty->assign("not_invoiced", $not_invoiced);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);

$smarty->display("print_orders.tpl");
