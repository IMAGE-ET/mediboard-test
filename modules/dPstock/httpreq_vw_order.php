<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$order_id = CValue::get('order_id');

// Loads the expected Order
$order = new CProductOrder();
$order->load($order_id);
$order->loadRefsBack();
$order->loadRefsFwd();
$order->updateCounts();

if ($order->_ref_object) {
  $order->_ref_object->loadRefsFwd();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('order', $order);

if (!$order->date_ordered)
  $smarty->display('inc_order.tpl');
else
  $smarty->display('inc_order_to_receive.tpl');
