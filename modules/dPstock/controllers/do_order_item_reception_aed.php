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
  $reference = new CProductReference;
  $reference->reference_id = $reference_id;
  
  if (!$reference_id || !$reference->loadMatchingObject()) {
    CAppUI::setMsg("Impossible de créer l'article, la réference n'existe pas", UI_MSG_ERROR);
  }
  
  $order_item = new CProductOrderItem;
  $order_item->reference_id = $reference_id;
  $order_item->quantity = $quantity;
  $order_item->unit_price = $reference->_cond_price;
  if ($msg = $order_item->store()) {
    CAppUI::setMsg($msg);
  }
  
  $_POST["order_item_id"] = $order_item->_id;
}

$do->doIt();

?>