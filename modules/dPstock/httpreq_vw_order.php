<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id  = mbGetValueFromGet('order_id');
$action    = mbGetValueFromGet('action');
$object_id = mbGetValueFromGet('object_id');

// Loads the expected Order
$order = new CProductOrder();
$order->load($order_id);

if ($object_id && $order->_id) {
  switch ($action) {
    case 'add': // add a reference to the order
      $order_item = new CProductOrderItem();
      
      $where = array();
      $where['reference_id'] = " = $object_id";
      $where['order_id'] = " = $order->_id";

      if ($order_item->loadObject($where)) {
          $order_item->quantity++;
      } else {
        $reference = new CProductReference();
        $reference->load($object_id);
        if ($reference->_id) {
          $order_item->order_id = $order_id;
          $order_item->reference_id = $reference->_id;
          $order_item->quantity = 1;
          $order_item->unit_price = $reference->price;
        }
      }
      break;
      
    case 'inc': // increments the quantity of this item
      $order_item = new CProductOrderItem();
      if ($order_item->load($object_id)) {
          $order_item->quantity++;
      }
      break;
      
    case 'dec': // decrements the quantity of this item
      $order_item = new CProductOrderItem();
      if ($order_item->load($object_id)) {
        if ($order_item->quantity > 0){
          $order_item->quantity--;
        } else {
          $order_item->delete();
        }
      }
      break;
      
    case 'delete': // delete an order item
      $order_item = new CProductOrderItem();
      $order_item->load($object_id);
      $order_item->delete();
      //mbTrace($order_item);
      break;
  }
  if ($action != 'delete') {
    $order_item->store();
  }
  $order->load($order_id);
  $order->loadRefsBack();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order', $order);

$smarty->display('inc_vw_order.tpl');
?>
