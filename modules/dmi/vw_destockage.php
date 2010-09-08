<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$ljoin = array(
  "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
  "product_reference"  => "product_reference.reference_id = product_order_item.reference_id",
  "product"            => "product.product_id = product_reference.product_id",
);

$where = array(
  "product_order_item_reception.lapsing_date" => "IS NOT NULL",
);

$reception = new CProductOrderItemReception;
$receptions = $reception->loadList($where, "lapsing_date", 30, null, $ljoin);

foreach($receptions as $_id => $_reception) {
  $qty = $_reception->getUnitQuantity();
  $_reception->_total_quantity = $qty;
  $_reception->_used_quantity = $_reception->countBackRefs('lines_dmi');
  $_reception->_remaining_quantity = $qty - $_reception->_used_quantity;
  $_reception->_new_order_item = new CProductOrderItem;
  
  $_reception->_ref_order_item->_ref_reference->loadRefSociete();
  $_reception->_ref_order_item->_ref_reference->_ref_product->loadBackrefs("references");
  
  foreach($_reception->_ref_order_item->_ref_reference->_ref_product->_back["references"] as $_reference) {
    $_reference->loadRefSociete();
  }
  
  $order_items = $_reception->loadBackRefs("order_items");
  foreach($order_items as $_order_item) {
    $_order_item->loadOrder();
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
$smarty->display("vw_destockage.tpl");
