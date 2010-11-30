<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$societe_id = CValue::getOrSession("societe_id");

$ljoin = array(
  "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
  "product_reference"  => "product_reference.reference_id = product_order_item.reference_id",
  "product"            => "product.product_id = product_reference.product_id",
  "dmi"                => "dmi.product_id = product.product_id",
);

$where = array(
  "product_order_item_reception.cancelled" => "='0'",
  "dmi.type" => " != 'purchase'",
);

$societe = new CSociete;
if (!$societe->load($societe_id)) {
  $where["product_order_item_reception.lapsing_date"] = "IS NOT NULL";
}
else {
  $where["product_reference.societe_id"] = "= '$societe_id'";
}

$product = new CProduct;
$product->societe_id = $societe_id;
$product->loadRefsFwd();

$reception = new CProductOrderItemReception;
$receptions = $reception->loadList($where, ($societe->_id ? "product.name" : "lapsing_date"), ($societe->_id ? 500 : 100), null, $ljoin);

foreach($receptions as $_id => $_reception) {
  $qty = $_reception->getUnitQuantity();
  $_reception->_total_quantity = $qty;
  $_reception->_used_quantity = $_reception->countBackRefs('lines_dmi');
  $_reception->_remaining_quantity = $qty - $_reception->_used_quantity;
  $_reception->_new_order_item = new CProductOrderItem;
  
  $_reception->loadRefOrderItem();
  $_reception->_ref_order_item->loadReference();
  $_reception->_ref_order_item->_ref_reference->loadRefSociete();
  $_reception->_ref_order_item->_ref_reference->_ref_product->loadBackrefs("references");
  
  foreach($_reception->_ref_order_item->_ref_reference->_ref_product->_back["references"] as $_reference) {
    $_reference->loadRefSociete();
  }
  
  $_reception->loadBackRefs("order_items");
  $order_items = &$_reception->_back["order_items"];
  
  foreach($order_items as $_order_item_id => $_order_item) {
    $_order_item->loadOrder();
    if ($_order_item->_ref_order->object_id || 
        $_order_item->_ref_order->date_ordered || 
        $_order_item->_ref_order->received || 
        $_order_item->_ref_order->cancelled || 
        $_order_item->_ref_order->deleted || 
        strpos(CProductOrder::$_return_form_label, $_order_item->_ref_order->comments) !== 0) 
      unset($order_items[$_order_item_id]);
  }
  
  if ($_reception->_remaining_quantity < 1) {
    unset($receptions[$_id]);
  }
  else {
    $dmi = CDMI::getFromProduct($_reception->_ref_order_item->_ref_reference->_ref_product);
    $_reception->_ref_dmi = $dmi;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receptions", $receptions);
$smarty->assign("product", $product);
$smarty->display("vw_destockage.tpl");
