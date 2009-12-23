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

$service_id  = CValue::get('service_id');
$keywords    = CValue::get('keywords');
$limit       = CValue::get('limit');

// Service's stocks
$where = array();
if ($service_id) {
  $where['product_stock_service.service_id'] = " = $service_id";
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
$list_stocks       = $stock->loadList($where, $orderby, $limit?$limit:30, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('stock',             $stock);
$smarty->assign('list_stocks',       $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);

$smarty->display('inc_stocks_list.tpl');

?>