<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m, $g;

$can->needsRead();

$category_id = mbGetValueFromGetOrSession('category_id');
$keywords    = mbGetValueFromGet('keywords');
$order_id    = mbGetValueFromGet('order_id');

$sql  = 'SELECT product.* FROM product ';
$sql .= 'LEFT JOIN product_stock ON product.product_id = product_stock.product_id ';
$sql .= 'WHERE ';
if ($category_id) {
  $sql .= "product.category_id = '$category_id' AND ";
}
if ($keywords) {
  $sql .= "product.name LIKE '%$keywords%' OR ";
  $sql .= "product.description LIKE '%$keywords%' AND ";
}
$sql .= "product_stock.group_id = $g ";
$sql .= 'ORDER BY product.name ASC';

$product = new CProduct();
$list_products = $product->loadQueryList($sql);
foreach($list_products as $prod) {
	$prod->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);
$smarty->assign('order_id', $order_id);

$smarty->display('inc_products_list.tpl');
?>
