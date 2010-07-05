<?php /* $Id: do_order_item_aed.php 7914 2010-01-25 13:56:02Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7914 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CProductOrderItem');

if(CValue::post("_create_order")) {
  $reference_id = CValue::post("reference_id");
  $reference = new CProductReference;
  $reference->reference_id = $reference_id;
  
  if (!$reference_id || !$reference->loadMatchingObject()) {
    CAppUI::setMsg("Impossible de créer l'article, la réference n'existe pas", UI_MSG_ERROR);
  }
  
  $where = array(
    "product_order.societe_id" => "= '$reference->societe_id'",
    "product_order.object_class" => "IS NULL",
  );
  
  // If a context is provided
  if ($context_guid = CValue::post("_context_guid")) {
    list($object_class, $object_id) = explode("-", $context_guid);
    $where["product_order.object_class"] = "= '$object_class'";
    $where["product_order.object_id"]    = "= '$object_id'";
  }
  
  if (CAppUI::conf("dPstock group_independent") == 0) {
    $where["product_order.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
  }
	
  $order = new CProductOrder;
  $orders = $order->search("waiting", null, 1, $where);

  if ($context_guid && count($orders) == 0) {
    $orders = $order->search("locked", null, 1, $where);
  }

  // If no order found
  if (count($orders) == 0) {
    if ($context_guid) {
      $context = CMbObject::loadFromGuid($context_guid);
      $order->setObject($context);
      $order->locked = 1;
    }
    
    $order->societe_id = $reference->societe_id;
    $order->group_id = CGroups::loadCurrent()->_id;
    
    if ($msg = $order->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
    
    $order->order_number = $order->getUniqueNumber();
    
    if ($msg = $order->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
  }
  else {
    $order = reset($orders);
  }
  
  if ($order->_id && !$order->bill_number) {
    $order->bill_number = CValue::post("_bill_number");
    $order->store();
  }
  
  $_POST["order_id"] = $order->_id;
}

$do->doIt();
