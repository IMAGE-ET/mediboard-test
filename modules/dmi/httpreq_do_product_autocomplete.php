<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$keywords    = CValue::post("_view");
$category_id = CValue::post("category_id");

$where = array();
if($category_id){
  $where["category_id"] = " = '$category_id'";
}

$product = new CProduct();
$matches = $product->seek($keywords, $where, 30, false, null, "name");

foreach($matches as $_product) {
  $ljoin = array(
    "product_order_item" => "product_order_item.order_item_id = product_order_item.order_item_id",
    "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
    "product"            => "product_reference.product_id = product.product_id",
    //"product_order_item_reception" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
  );
  
  $where = array(
    "product.product_id" => "= '$_product->_id'"
  );
  
  $reception = new CProductOrderItemReception;
  $receptions = $reception->loadList($where, "date", null, null, $ljoin);
  
  $total = 0;
  foreach($receptions as $_reception) {
    $item = $_reception->_ref_order_item;
    $item->loadReference();
    $reference = $item->_ref_reference;
    $total += $item->quantity * $reference->quantity * $_product->quantity;
  }
  
  $line_dmi = new CPrescriptionLineDMI;
  $line_dmi->product_id = $_product->_id;
  $used = $line_dmi->countMatchingList();
  
  $_product->_available_quantity = $total - $used;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_product_autocomplete.tpl");
