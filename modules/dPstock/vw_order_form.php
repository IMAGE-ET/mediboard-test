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

$order_id    = CValue::getOrSession('order_id');

// Loads the expected Order
$order = new CProductOrder();

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  $order->loadRefsFwd();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('order', $order);
$smarty->display('vw_order_form.tpl');

?>