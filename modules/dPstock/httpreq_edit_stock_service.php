<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$stock_service_id = CValue::get('stock_service_id');
$service_id       = CValue::get('service_id');
$product_id       = CValue::get('product_id');

// Loads the stock 
$stock = new CProductStockService();

// If stock_id has been provided, we load the associated product
if ($stock_service_id) {
  $stock->load($stock_service_id);
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();
}
else if ($product_id) {
  $product = new CProduct();
  $product->load($product_id);
  
  $stock->product_id = $product_id;
  $stock->_ref_product = $product;
  $stock->updateFormFields();
}
else {
  $stock->loadRefsFwd(); // pour le _ref_product
}

$list_services = CProductStockGroup::getServicesList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock', $stock);
$smarty->assign('service_id',  $service_id);
$smarty->assign('list_services',   $list_services);

$smarty->display('inc_edit_stock_service.tpl');

