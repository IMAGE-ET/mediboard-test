<?php /* $Id: vw_aed_order.php 7645 2009-12-17 16:40:57Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7645 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$order = new CProductOrder;
$list_orders = $order->search("waiting", null, 30);

foreach($list_orders as $_order) {
	$_order->countBackRefs("order_items");
  $_order->loadRefsOrderItems();
  $_order->updateCounts();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('list_orders', $list_orders);
$smarty->display('inc_orders_tabs.tpl');
