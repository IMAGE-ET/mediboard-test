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
 
CCanDo::checkRead();

$type = CValue::post("type", "pending");
$keywords = CValue::post("keywords");

$order = new CProductOrder;
$orders_list = $order->search($type, $keywords, 30);

foreach ($orders_list as $_order) {
  $_order->countBackRefs("order_items");
  $_order->updateCounts();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("orders", $orders_list);
$smarty->display("inc_orders_autocomplete.tpl");
