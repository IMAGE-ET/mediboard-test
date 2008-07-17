<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

global $can, $g;

$can->needsAdmin();

$stock_id         = mbGetValueFromGetOrSession('stock_id');
$category_id      = mbGetValueFromGetOrSession('category_id');
$function_id      = mbGetValueFromGetOrSession('function_id');

// Loads the stock 
$stock_service = new CProductStockService();

// If stock_id has been provided, we load the associated product
if ($stock_id) {
  $stock_service->stock_id = $stock_id;
  $stock_service->loadMatchingObject();
  $stock_service->loadRefsFwd();
  $stock_service->_ref_product->loadRefsFwd();
}
$stock_service->updateFormFields();

// Categories list
$list_categories = new CProductCategory();
$list_categories = $list_categories->loadList(null, 'name');

// Functions list
$where = array('group_id' => "= $g");
$list_functions = new CFunctions();
$list_functions = $list_functions->loadList($where, 'text');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock_service', $stock_service);

$smarty->assign('category_id', $category_id);
$smarty->assign('function_id', $function_id);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_functions',  $list_functions);

$smarty->display('vw_idx_stock_service.tpl');

?>