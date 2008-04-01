<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

$stock_id    = mbGetValueFromGetOrSession('stock_id');
$product_id  = mbGetValueFromGetOrSession('product_id');
$category_id = mbGetValueFromGetOrSession('category_id');
$only_ordered_stocks = mbGetValueFromGet('only_ordered_stocks');

// Loads the stock in function of the stock ID or the product ID
$stock = new CProductStock();

// If stock_id has been provided, we load the associated product
if ($stock_id) {
  $stock->stock_id = $stock_id;
  $stock->loadMatchingObject();
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();

// else, if a product_id has been provided, we load the associated stock
} else if($product_id) {
	$product = new CProduct();
  $product->load($product_id);
  
  $stock->product_id = $product_id;
  $stock->_ref_product = $product;
} else {
  $stock->loadRefsFwd();
}
$stock->updateFormFields();

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');
if (!$category_id) {
  $category = $stock->_ref_product->_ref_category;
} else {
  $category->category_id = $category_id;
  $category->loadMatchingObject();
}

if ($category) {
	$category->loadRefs();
	
	// Loads the products list
	foreach($category->_ref_products as $prod) {
	  $prod->loadRefs();
	  $prod->loadRefStock();
	}
} else {
	$category = new CProductCategory();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock',           $stock);
$smarty->assign('category',        $category);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('only_ordered_stocks', $only_ordered_stocks);

$smarty->display('vw_idx_stock.tpl');

?>