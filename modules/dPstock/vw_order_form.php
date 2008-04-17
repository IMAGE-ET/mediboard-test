<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id    = mbGetValueFromGetOrSession('order_id');

// Loads the expected Order
$order = new CProductOrder();

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  
  foreach ($order->_ref_order_items as $item) {
    $item->_quantity_received = $item->quantity_received;
  }
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order', $order);

$smarty->display('vw_order_form.tpl');

?>
