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
CCanDo::checkRead();

$category_id = CValue::get('category_id');
$keywords    = CValue::get('keywords');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = '$category_id'";
}

if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}

$where['product_stock_group.group_id'] = " = '".CProductStockGroup::getHostGroup()."'";

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock_group.product_id';

$orderby = 'product.name ASC';

$stock = new CProductStockGroup();
$list_stocks = $stock->loadList($where, $orderby, 20, null, $leftjoin);

$smarty = new CSmartyDP();
$smarty->assign('list_stocks', $list_stocks);
$smarty->display('inc_aed_delivery_stocks_list.tpl');
