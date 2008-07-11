<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m, $g;

$can->needsEdit();

$category_id         = mbGetValueFromGet('category_id');
$keywords            = mbGetValueFromGet('keywords');
$limit               = mbGetValueFromGet('limit');
$only_ordered_stocks = mbGetValueFromGet('only_ordered_stocks')=='true';

$where = array();
if ($g) {
  $where['product_stock.group_id'] = " = $g";
}
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$orderby = 'product.name ASC';

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock.product_id'; // product to stock

if ($only_ordered_stocks) {
  $leftjoin['product_reference']    = 'product_reference.product_id = product_stock.product_id'; // stock to reference
  $leftjoin['product_order_item']   = 'product_order_item.reference_id = product_reference.reference_id'; // reference to order item
  $leftjoin['product_order']        = 'product_order.order_id = product_order_item.order_id'; // order item to order
  $where['product_order.cancelled'] = ' = 0'; // order not cancelled
  $where['product_order_item.quantity_received'] = ' < product_order_item.quantity'; // order item not received yet
}
$stock = new CProductStock();
$list_stocks_count = $stock->countList($where, $orderby, null, null, $leftjoin);
$list_stocks = $stock->loadList($where, $orderby, $limit?$limit:20, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);

$smarty->display('inc_stocks_list.tpl');
?>
