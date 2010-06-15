<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$type = CValue::getOrSession('type');
$keywords = CValue::getOrSession('keywords');
$category_id = CValue::getOrSession('category_id');

$where = array();

if ($category_id) {
  $where["product.category_id"] = "= '$category_id'";
}

$order = new CProductOrder;
$orders = $order->search($type, $keywords, 200, $where);

foreach($orders as $_order) {
  $_order->updateCounts();
  if ($_order->object_id) {
    $_order->loadTargetObject();
    $_order->_ref_object->loadRefsFwd();
  }
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders);
$smarty->assign('type',   $type);

$smarty->display('inc_orders_list.tpl');
