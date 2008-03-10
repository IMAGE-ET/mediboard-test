<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

// Gets objects ID from Get or Session
$product_id  = mbGetValueFromGetOrSession('product_id', null);
$category_id = mbGetValueFromGetOrSession('category_id', null);

// Loads the required Product and its References
$product = new CProduct();
if ($product->load($product_id)) {
  $product->loadRefsBack();
  
  foreach($product->_ref_references as $key => $value) {
    $value->loadRefsBack();
  }
}

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');
if (!$category_id) {
  $category = $product->_ref_category;
} else {
  $category->category_id = $category_id;
  $category->loadMatchingObject();
}

if ($category) {
  $category->loadRefsBack();
  
  // Loads the products list
  foreach($category->_ref_products as $prod) {
    $prod->loadRefsBack();
  }
} else $category = new CProductCategory();

// If a null product is called, we set him the provided category ID
if (!$product->_id) {
  $product->category_id = $category->_id;
}

// Loads the manufacturers list
$societe = new CSociete();
$list_societes = $societe->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('product',         $product);
$smarty->assign('category',        $category);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);

$smarty->display('vw_idx_product.tpl');

?>