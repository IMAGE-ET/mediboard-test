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

$stock_id    = CValue::getOrSession('stock_service_id');
$category_id = CValue::get('category_id');
$object_id   = CValue::get('object_id');
$keywords    = CValue::get('keywords');
$start       = CValue::get('start');

CValue::setSession('category_id', $category_id);

$where = array(
  "service.group_id" => "= '".CProductStockGroup::getHostGroup()."'",
);

if ($object_id) {
  $where['product_stock_service.object_id']    = " = '$object_id'";
  $where['product_stock_service.object_class'] = " = 'CService'"; // XXX
}
if ($category_id) {
  $where['product.category_id'] = " = '$category_id'";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}

$leftjoin = array(
  "product" => "product.product_id = product_stock_service.product_id", // product to stock
  "service" => "service.service_id = product_stock_service.object_id",
);

$stock = new CProductStockService();
$list_stocks_count = $stock->countList($where, null, $leftjoin);

$pagination_size = CAppUI::conf("dPstock CProductStockService pagination_size");
$list_stocks = $stock->loadList($where, 'product.name ASC', intval($start).",$pagination_size", null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('stock',             $stock);
$smarty->assign('stock_id',          $stock_id);
$smarty->assign('list_stocks',       $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);
$smarty->assign('start',             $start);

$smarty->display('inc_stocks_list.tpl');
