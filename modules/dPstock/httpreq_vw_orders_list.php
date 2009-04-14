<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can, $g;
$can->needsRead();

$ds = CSQLDataSource::get('std');

$type     = mbGetValueFromGetOrSession('type');
$keywords = mbGetValueFromGetOrSession('keywords');

$order = new CProductOrder();
$societe = new CSociete();

$where = array();
$leftjoin = array();
$leftjoin['product_order_item'] = 'product_order.order_id = product_order_item.order_id';

// if keywords have been provided
if ($keywords) {
	$where_or = array();
	
	// we seek among the societes
  foreach ($societe->getSeekables as $field => $spec) {
    $where_societe_or[] = "societe.$field LIKE '%$keywords%'";
  }
  $where_societe[] = implode(' OR ', $where_societe_or);
	
	// we seek among the orders
	foreach($order->getSeekables() as $field => $spec) {
	  $where_or[] = "product_order.$field LIKE '%$keywords%'";
	}
	$where_or[] = 'product_order.societe_id ' . $ds->prepareIn(array_keys($societe->loadList($where_societe)));
	$where[] = implode(' OR ', $where_or);
}

$orderby = 'product_order.date_ordered DESC, product_order_item_reception.date DESC';
$where['product_order.deleted'] = " = 0";
$where['product_order.cancelled'] = " = 0";
$where['product_order.locked'] = " = 0";
$where['product_order.date_ordered'] = "IS NULL";

$leftjoin['product_order_item_reception'] = 
      'product_order_item.order_item_id = product_order_item_reception.order_item_id';

switch ($type) {
	case 'waiting': break;
  case 'locked':
  	$where['product_order.locked'] = " = 1";
  	break;
	case 'pending': // pending or received are the same here but they are sorted thanks to PHP
  case 'received':
    $where['product_order.locked'] = " = 1";
    $where['product_order.date_ordered'] = "IS NOT NULL";
    break;
  default:
  case 'cancelled':
    $where['product_order.cancelled'] = " = 1";
    unset($where['product_order.locked']);
    unset($where['product_order.date_ordered']);
		break;
}

if ($g) {
  $where['product_order.group_id'] = " = $g";
}
$orders_list = $order->loadList($where, $orderby, 20, null, $leftjoin);

if ($type == 'pending') {
  $list = array();
  foreach ($orders_list as $order) {
    if ($order->countReceivedItems() < count($order->_ref_order_items)) {
      $list[] = $order;
    }
  }
  $orders_list = $list;
}

if ($type == 'received') {
  $list = array();
  foreach ($orders_list as $order) {
    if ($order->countReceivedItems() >= count($order->_ref_order_items)) {
      $list[] = $order;
    }
  }
  $orders_list = $list;
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders_list);
$smarty->assign('type',   $type);

$smarty->display('inc_orders_list.tpl');
?>
