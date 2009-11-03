<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can, $g;
$can->needsEdit();

$category_id         = CValue::get('category_id');
$keywords            = CValue::get('keywords');
$limit               = CValue::get('limit');
$only_ordered_stocks = CValue::get('only_ordered_stocks')=='true';

CValue::setSession('category_id', $category_id);

$where = array();
if ($g) {
  $where['product_stock_group.group_id'] = " = $g";
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
$leftjoin['product'] = 'product.product_id = product_stock_group.product_id'; // product to stock

if ($only_ordered_stocks) {
  $where['product_order.cancelled'] = '= 0'; // order not cancelled
  $where['product_order.deleted']   = '= 0'; // order not deleted
  $leftjoin['product_reference']    = 'product_reference.product_id = product_stock_group.product_id'; // stock to reference
  $leftjoin['product_order_item']   = 'product_order_item.reference_id = product_reference.reference_id'; // reference to order item
  $leftjoin['product_order']        = 'product_order.order_id = product_order_item.order_id'; // order item to order
  $where[] = 'product_order_item.order_item_id NOT IN (
    SELECT product_order_item.order_item_id FROM product_order_item
    LEFT JOIN product_order_item_reception ON product_order_item_reception.order_item_id = product_order_item.order_item_id
    LEFT JOIN product_order ON product_order.order_id = product_order_item.order_id
    WHERE product_order.deleted = 0 AND product_order.cancelled = 0
    HAVING SUM(product_order_item_reception.quantity) < product_order_item.quantity
  )';
}

$stock = new CProductStockGroup();
$list_stocks_count = $stock->countList($where, $orderby, null, null, $leftjoin);
$list_stocks = $stock->loadList($where, $orderby, $limit?$limit:20, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('stock',             $stock);
$smarty->assign('list_stocks',       $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);

$smarty->display('inc_stocks_list.tpl');
?>
