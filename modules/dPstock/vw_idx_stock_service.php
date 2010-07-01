<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;
CCanDo::checkEdit();

$stock_service_id = CValue::getOrSession('stock_service_id');
$category_id      = CValue::getOrSession('category_id');
$service_id       = CValue::getOrSession('service_id');
$product_id       = CValue::get('product_id');

// Loads the stock 
$stock = new CProductStockService();

// If stock_id has been provided, we load the associated product
if ($stock_service_id) {
  $stock->stock_id = $stock_service_id;
  $stock->loadMatchingObject();
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();
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

// Categories list
$list_categories = new CProductCategory();
$list_categories = $list_categories->loadList(null, 'name');

// Functions list
$where = array('group_id' => "= $g");
$service = new CService();
$list_services = $service->loadListWithPerms(PERM_READ, $where, "nom");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock', $stock);

$smarty->assign('category_id', $category_id);
$smarty->assign('service_id',  $service_id);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_services',   $list_services);

$smarty->display('vw_idx_stock_service.tpl');

?>