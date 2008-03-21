<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$reference_id = mbGetValueFromGetOrSession('reference_id');
$societe_id   = mbGetValueFromGetOrSession('societe_id');
$category_id  = mbGetValueFromGetOrSession('category_id');
$product_id   = mbGetValueFromGetOrSession('product_id');

// Loads the expected Reference
$reference = new CProductReference();

// If a reference ID has been provided, 
// we load it and its associated product
if ($reference_id) {
  $reference->reference_id = $reference_id;
  $reference->loadMatchingObject();
  $reference->loadRefsFwd();
  $reference->_ref_product->loadRefsFwd();

// else, if a product_id has been provided, 
// we load it and its associated reference
} else if($product_id) {
  $reference->product_id = $product_id;
  $product = new CProduct();
  $product->load($product_id);
  $reference->_ref_product = $product;

} else {
  // If a supplier ID is provided, we make a corresponding reference
  if ($societe_id) {
    $reference->societe_id = $societe_id;
  } else if ($category_id) {
    $reference->_ref_product = new CProduct();
    $reference->_ref_product->category_id = $category_id;
    $reference->_ref_product->loadMatchingObject();
  }
}
$reference->loadRefsFwd();

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
  }
} else {
  $category = new CProductCategory();
}


// Suppliers list
$societe = new CSociete();
$list_societes = $societe->loadList();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('reference',       $reference);
$smarty->assign('category',        $category);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);

$smarty->display('vw_idx_reference.tpl');
?>
