<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$stock_id    = CValue::getOrSession('stock_service_id');
$category_id = CValue::get('category_id');
$service_id  = CValue::get('service_id');
$keywords    = CValue::get('keywords');

// Often valued as empty string
$limit               = CValue::first(CValue::get('limit'), "30");

CValue::setSession('category_id', $category_id);

$where = array();
if ($service_id) {
  $where['product_stock_service.service_id'] = " = $service_id";
}
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$orderby = 'product.name ASC';

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock_service.product_id'; // product to stock

$stock = new CProductStockService();
$list_stocks_count = $stock->countList($where, $orderby, null, null, $leftjoin);
$list_stocks = $stock->loadList($where, $orderby, $limit, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('stock',             $stock);
$smarty->assign('stock_id',          $stock_id);
$smarty->assign('list_stocks',       $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);

$smarty->display('inc_stocks_list.tpl');

?>