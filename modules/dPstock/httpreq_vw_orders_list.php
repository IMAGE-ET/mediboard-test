<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$type        = CValue::get('type');
$keywords    = CValue::get('keywords');
$category_id = CValue::get('category_id');
$invoiced    = CValue::get('invoiced');
$start       = CValue::get('start', array());

$page = CValue::read($start, $type, 0);

$where = array();

if ($category_id) {
  $where["product.category_id"] = "= '$category_id'";
}

if (($type == "received") && !$invoiced) {
  $where["bill_number"] = "IS NULL";
}

// @todo faire de la pagination
$order = new CProductOrder;
$orders = $order->search($type, $keywords, "$page, 25", $where);

$count = $order->_search_count;

foreach($orders as $_order) {
  //$_order->updateCounts();
  if ($_order->object_id) {
    $_order->loadTargetObject();
    $_order->_ref_object->loadRefsFwd();
  }
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders);
$smarty->assign('count', $count);
$smarty->assign('type', $type);
$smarty->assign('page', $page);
$smarty->assign('invoiced', $invoiced);

$smarty->display('inc_orders_list.tpl');
