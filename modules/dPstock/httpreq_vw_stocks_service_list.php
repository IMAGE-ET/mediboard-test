<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision$
 *  @author Fabien Ménager
 */
 
global $can;
$can->needsEdit();

$category_id = mbGetValueFromGet('category_id');
$service_id  = mbGetValueFromGet('service_id');
$keywords    = mbGetValueFromGet('keywords');
$limit       = mbGetValueFromGet('limit');

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
$list_stocks = $stock->loadList($where, $orderby, $limit?$limit:20, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);

$smarty->display('inc_stocks_service_list.tpl');

?>