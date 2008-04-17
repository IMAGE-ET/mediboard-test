<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m, $g;

$can->needsRead();

$category_id         = mbGetValueFromGet('category_id');
$keywords            = mbGetValueFromGet('keywords');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$where['product_stock.group_id'] = " = $g";

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock.product_id';

$orderby = 'product.name ASC';

$stock = new CProductStock();
$list_stocks = $stock->loadList($where, $orderby, 20, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $list_stocks);

$smarty->display('inc_aed_stock_out_stocks_list.tpl');
?>
