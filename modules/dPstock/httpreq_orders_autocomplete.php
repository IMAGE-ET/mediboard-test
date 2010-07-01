<?php /* $Id: httpreq_vw_orders_list.php 7211 2009-11-03 12:27:08Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7211 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$type = CValue::post("type", "pending");
$keywords = CValue::post("keywords");

$order = new CProductOrder;
$orders_list = $order->search($type, $keywords, 30);

foreach($orders_list as $_order){
  $_order->countBackRefs("order_items");
  $_order->updateCounts();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("orders", $orders_list);
$smarty->display("inc_orders_autocomplete.tpl");
