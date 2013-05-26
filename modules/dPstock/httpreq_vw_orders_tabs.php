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

$order = new CProductOrder;
$list_orders = $order->search("waiting", null, 30);

foreach ($list_orders as $_order) {
  $_order->countBackRefs("order_items");
  $_order->loadRefsOrderItems();
  $_order->updateCounts();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('list_orders', $list_orders);
$smarty->display('inc_orders_tabs.tpl');
