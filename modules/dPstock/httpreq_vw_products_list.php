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
$societe_id  = mbGetValueFromGetOrSession('societe_id');
$keywords    = mbGetValueFromGet('keywords');

$sql  = 'SELECT product.* FROM product ';
$sql .= 'WHERE ';
if ($category_id) {
  $sql .= "product.category_id = '$category_id' AND ";
}
if ($societe_id) {
  $sql .= "product.societe_id = '$societe_id' AND ";
}
if ($keywords) {
	$sql .= "product.code LIKE '%$keywords%' OR ";
  $sql .= "product.name LIKE '%$keywords%' OR ";
  $sql .= "product.description LIKE '%$keywords%' AND ";
}
$sql .= '1 ';
$sql .= 'ORDER BY product.name ASC';

$product = new CProduct();
$list_products = $product->loadQueryList($sql);
foreach($list_products as $prod) {
	$prod->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_products',   $list_products);

$smarty->display('inc_products_list.tpl');
?>
