<?php /* $Id: do_order_item_reception_aed.php 8015 2010-02-05 10:22:51Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8015 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CProductOrderItemReception', 'order_item_reception_id');

$reference_id = CValue::post("_reference_id");
$quantity     = CValue::post("quantity");

if($reference_id) {
  // If it is a societe id
  if(!is_numeric($reference_id)) {
    list($societe_id, $product_id) = explode("-", $reference_id);
    
    $societe = new CSociete;
    $societe->load($societe_id);
    
    $product = new CProduct;
    $product->load($product_id);
    
    $reference = new CProductReference;
    $reference->product_id = $product->_id;
    $reference->societe_id = $societe->_id;
    $reference->quantity = 1;
    $reference->price = 0;
    $reference->store();
  }
  else {
    // If it is a reference id
    $reference = new CProductReference;
    $reference->load($reference_id);
  }
  
  if (!$reference->_id) {
    CAppUI::setMsg("Impossible de créer l'article, la réference n'existe pas", UI_MSG_ERROR);
  }
  
  $order_item = new CProductOrderItem;
  $order_item->reference_id = $reference->_id;
  $order_item->quantity = $quantity;
  $order_item->unit_price = $reference->_cond_price;
  if ($msg = $order_item->store()) {
    CAppUI::setMsg($msg);
  }
  
  $_POST["order_item_id"] = $order_item->_id;
}

$do->doIt();

?>