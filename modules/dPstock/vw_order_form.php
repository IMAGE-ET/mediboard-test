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

$order->_is_loan = false;

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
    $_item->loadRefLot();
    
    $dmi = CDMI::getFromProduct($_item->_ref_reference->_ref_product);
    if ($dmi->_id && $dmi->type == "loan") {
      $order->_is_loan = true;
    }
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
$smarty->assign('pharmacien', $pharmacien);
$smarty->display('vw_order_form.tpl');

?>