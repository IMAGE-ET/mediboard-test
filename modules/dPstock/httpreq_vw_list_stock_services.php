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

$product_id = CValue::get('product_id');

$product = new CProduct;
$product->load($product_id);

$service = new CService;
$where = array(
  "group_id" => "= '$g'"
);
$list_services = $service->loadListWithPerms(PERM_READ, $where, "nom");

foreach($list_services as $_service) {
  $stock_service = new CProductStockService;
  $stock_service->service_id = $_service->_id;
  $stock_service->product_id = $product->_id;
  if (!$stock_service->loadMatchingObject()) {
    $stock_service->quantity = $product->quantity;
    $stock_service->order_threshold_min = $product->quantity;
  }
  $_service->_ref_stock = $stock_service;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_services', $list_services);

$smarty->display('inc_list_stock_services.tpl');
