<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

global $can, $g;

$can->needsAdmin();

$stock_service_id = mbGetValueFromGetOrSession('stock_service_id');
$category_id      = mbGetValueFromGetOrSession('category_id');
$service_id       = mbGetValueFromGetOrSession('service_id');

// Loads the stock 
$stock = new CProductStockService();

// If stock_id has been provided, we load the associated product
if ($stock_service_id) {
  $stock->stock_id = $stock_service_id;
  $stock->loadMatchingObject();
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();
}
$stock->updateFormFields();

// Categories list
$list_categories = new CProductCategory();
$list_categories = $list_categories->loadList(null, 'name');

// Functions list
$where = array('group_id' => "= $g");
$list_services = new CService();
$list_services = $list_services->loadList($where, 'nom');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock', $stock);

$smarty->assign('category_id', $category_id);
$smarty->assign('service_id',  $service_id);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_services',   $list_services);

$smarty->display('vw_idx_stock_service.tpl');

?>