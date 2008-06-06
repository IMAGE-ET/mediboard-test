<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$category_id = mbGetValueFromGetOrSession('category_id');
$societe_id  = mbGetValueFromGetOrSession('societe_id');
$keywords    = mbGetValueFromGet('keywords');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = '$category_id'";
}
if ($societe_id) {
  $where['product.societe_id'] = " = '$societe_id'";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$orderby = 'product.name ASC';

$product = new CProduct();
$list_products = $product->loadList($where, $orderby, 20);
foreach($list_products as $prod) {
	$prod->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_products',   $list_products);

$smarty->display('inc_products_list.tpl');
?>
