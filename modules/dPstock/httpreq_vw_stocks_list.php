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
$only_ordered_stocks = mbGetValueFromGet('only_ordered_stocks');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($keywords) {
  $where['product.name'] = " LIKE '%$keywords%'";
}
$where['product_stock.group_id'] = " = $g";

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock.product_id';

$orderby = 'product.name ASC';

$stock = new CProductStock();
$list_stocks = $stock->loadList($where, $orderby, 20, null, $leftjoin);

if ($only_ordered_stocks == 'on') {
	$filtered_stocks = array();
	foreach($list_stocks as $sto) {
		$sto->loadRefOrders();
		if ($sto->_ordered_count > 0) {
	    $filtered_stocks[] = $sto;
		}
	}
} else {
	$filtered_stocks = $list_stocks;
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $filtered_stocks);

$smarty->display('inc_stocks_list.tpl');
?>
