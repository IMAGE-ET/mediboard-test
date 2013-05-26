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

global $g;
CCanDo::checkEdit();

$product_id = CValue::get('product_id');

$product = new CProduct;
$product->load($product_id);

$list_services = CProductStockGroup::getServicesList();

foreach ($list_services as $_service) {
  $stock_service = CProductStockService::getFromProduct($product, $_service);
  if (!$stock_service->_id) {
    $stock_service->quantity = $product->quantity;
    $stock_service->order_threshold_min = $product->quantity;
  }
  $_service->_ref_stock = $stock_service;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_services', $list_services);

$smarty->display('inc_list_stock_services.tpl');
