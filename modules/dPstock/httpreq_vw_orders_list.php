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

$order = new CProductOrder;
$orders_list = $order->search($type, $keywords, 30);

foreach($orders_list as $_order) {
  $_order->updateCounts();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders_list);
$smarty->assign('type',   $type);

$smarty->display('inc_orders_list.tpl');
