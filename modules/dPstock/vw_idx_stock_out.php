<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

$stock_out_id = mbGetValueFromGetOrSession('stock_out_id');
$category_id = mbGetValueFromGetOrSession('category_id');

// Loads the stock in function of the stock ID or the product ID
$stock_out = new CProductStockOut();
$list_stock_outs = $stock_out->loadList(null, 'date');
if ($stock_out_id) {
  $stock_out->stock_out_id = $stock_out_id;
  $stock_out->loadMatchingObject();
  $stock_out->loadRefsFwd();
  $stock_out->_ref_stock->loadRefsFwd();
}

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');
if ($category_id) {
  $category->category_id = $category_id;
  $category->loadMatchingObject();
}

if ($category) {
  $category->loadRefs();
  
  // Loads the products list
  foreach($category->_ref_products as $prod) {
    $prod->loadRefs();
  }
} else $category = new CProductCategory();

$stock_out->quantity = 5;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('category',        $category);
$smarty->assign('list_categories', $list_categories);

$smarty->assign('stock_out',       $stock_out);

$smarty->display('vw_idx_stock_out.tpl');

?>