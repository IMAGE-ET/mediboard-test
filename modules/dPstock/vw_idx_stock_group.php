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

$stock_id    = CValue::getOrSession('stock_id');
$category_id = CValue::getOrSession('category_id');
$product_id  = CValue::get('product_id');
$letter      = CValue::getOrSession('letter', "%");

// Loads the stock in function of the stock ID or the product ID
$stock = new CProductStockGroup();

// If stock_id has been provided, we load the associated product
if ($stock_id) {
  $stock->stock_id = $stock_id;
  $stock->loadMatchingObject();
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();

// else, if a product_id has been provided, we load the associated stock
} 
else if($product_id) {
	$product = new CProduct();
  $product->load($product_id);
  
  $stock->product_id = $product_id;
  $stock->_ref_product = $product;
} 
else {
  $stock->loadRefsFwd();
}
$stock->updateFormFields();

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

$service = new CService;
$where = array(
  "group_id" => "= '$g'"
);
$list_services = $service->loadListWithPerms(PERM_READ, $where, "nom");

foreach($list_services as $_service) {
  $stock_service = new CProductStockService;
  $stock_service->service_id = $_service->_id;
  $stock_service->product_id = $stock->product_id;
  if (!$stock_service->loadMatchingObject()) {
    $stock_service->quantity = $stock->_ref_product->quantity;
    $stock_service->order_threshold_min = $stock->_ref_product->quantity;
    $stock_service->order_threshold_optimum = max($stock->getOptimumQuantity(), $stock_service->quantity);
  }
  $_service->_ref_stock = $stock_service;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock',           $stock);
$smarty->assign('category_id',     $category_id);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_services',   $list_services);
$smarty->assign('letter',          $letter);

$smarty->display('vw_idx_stock_group.tpl');

?>