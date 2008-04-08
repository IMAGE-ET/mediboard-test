<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;
$ds = CSQLDataSource::get('std');

$can->needsRead();

$type     = mbGetValueFromGetOrSession('type');
$keywords = mbGetValueFromGetOrSession('keywords');

$order = new CProductOrder();
$societe = new CSociete();

$where = array();

$leftjoin = array();
//$leftjoin['societe'] = 'product_order.societe_id = societe.societe_id';
$leftjoin['product_order_item'] = 'product_order.order_id = product_order_item.order_id';

// if keywords have been provided
if ($keywords) {
	$where_or = array();
	
	// we seek among the orders
	$seeks = $order->getSeeks();
	foreach($seeks as $col => $comp) {
	  $where_or[] = "product_order.$col $comp '%$keywords%'";
	}
	$where[] = implode(' OR ', $where_or);
	
	// we seek among the societes
  $seeks = $societe->getSeeks();
  foreach($seeks as $col => $comp) {
    $where_societe_or[] = "societe.$col $comp '%$keywords%'";
  }
  $where_societe[] = implode(' OR ', $where_societe_or);
  $where['product.societe_id'] = $ds->prepareIn(array_keys($societe->loadList($where_societe)));
}

$orderby = 'product_order.date_ordered DESC';

switch ($type) {
	 case 'waiting':
    $where['product_order.cancelled'] = " = 0";
    $where['product_order.locked'] = " = 0";
    $where['product_order.date_ordered'] = "IS NULL";
    break;
  case 'locked':
  	$where['product_order.cancelled'] = " = 0";
  	$where['product_order.locked'] = " = 1";
  	$where['product_order.date_ordered'] = "IS NULL";
  	break;
	case 'pending':
		$where['product_order.cancelled'] = " = 0";
		$where['product_order.locked'] = " = 1";
		$where['product_order.date_ordered'] = "IS NOT NULL";
		$where['product_order_item.date_received'] = "IS NULL";
		break;
  case 'received':
    $where['product_order.cancelled'] = " = 0";
    $where['product_order.date_ordered'] = "IS NOT NULL";
    $where['product_order_item.date_received'] = "IS NOT NULL";
    //$groupby = 'product_order`.`order_id';
    break;
  default:
  case 'cancelled':
    $where['product_order.cancelled'] = " = 1";
		break;
}
$orders_list = $order->loadList($where, $orderby, 20, (isset($groupby)?$groupby:null), $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders_list);
$smarty->assign('type',   $type);

$smarty->display('inc_orders_list.tpl');
?>
