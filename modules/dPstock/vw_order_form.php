<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$order_id    = CValue::getOrSession('order_id');

// Loads the expected Order
$order = new CProductOrder();
$septic = false;

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  $order->loadRefsFwd();
  $order->loadRefAddress();
  $order->updateCounts();
  
  if ($order->object_class) {
    $order->_ref_object->updateFormFields();
    $order->_ref_object->loadRefsFwd();
  }
  
  foreach ($order->_ref_order_items as $_item) {
    if ($_item->septic) $septic = true;
    $_item->loadRefLot();
  }
}

$pharmacien = new CMediusers;
$pharmaciens = $pharmacien->loadListFromType(array("Pharmacien"));
if (count($pharmaciens)) {
  $pharmacien = reset($pharmaciens);
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('order', $order);
$smarty->assign('septic', $septic);
$smarty->assign('pharmacien', $pharmacien);
$smarty->display('vw_order_form.tpl');

?>