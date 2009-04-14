<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can, $g;
$can->needsRead();

$category_id         = mbGetValueFromGet('category_id');
$keywords            = mbGetValueFromGet('keywords');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
if ($g) {
  $where['product_stock_group.group_id'] = " = $g";
}

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock_group.product_id';

$orderby = 'product.name ASC';

$stock = new CProductStockGroup();
$list_stocks = $stock->loadList($where, $orderby, 20, null, $leftjoin);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('list_stocks', $list_stocks);
$smarty->display('inc_aed_delivery_stocks_list.tpl');

?>