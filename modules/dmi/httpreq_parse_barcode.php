<?php 
/* $Id: httpreq_vw_list_categories.php 9329 2010-07-01 12:48:40Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: 9329 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkRead();

$barcode = CValue::get("barcode");

$parsed = CBarcodeParser::parse($barcode);
$comp = $parsed['comp'];
$strict = false;

$products = array();
$lots = array();

$lot = isset($comp['lot']) ? $comp['lot'] : trim($comp['raw'], ".%\n\r\t +-");
$dmi_category_id = CAppUI::conf("dmi CDMI product_category_id");

$object = new CProductOrderItemReception;
$where = array(
  "(product_order_item_reception.lapsing_date > '".mbDate()."' OR product_order_item_reception.lapsing_date IS NULL)",
  "(product_order_item_reception.code != '' AND product_order_item_reception.code IS NOT NULL)",
  "product.category_id" => "= '$dmi_category_id'",
);

$ljoin = array(
  "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
  "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
  "product"            => "product_reference.product_id = product.product_id",
);

$lots = $object->seek($lot, $where, 50, null, $ljoin);

foreach($lots as $_lot) {
  $strict = true;
  $_lot->loadRefOrderItem();
  $_lot->_ref_order_item->loadReference();
  $_lot->_ref_order_item->_ref_reference->loadRefProduct();
  $product = $_lot->_ref_order_item->_ref_reference->_ref_product;
  
  if (!isset($products[$product->_id])) {
    $product->_lots = array(
      $_lot->_id => $_lot,
    );
    $products[$product->_id] = $product;
  }
  else 
    $products[$product->_id]->_lots[$_lot->_id] = $_lot;
}

if ( empty($products) ) {
  $object = new CProduct;
  
  $keys = array("scc-prod", "ref", "cip", "raw");
  $values = array_intersect_key($comp, array_flip($keys));
  
  $where = array(
    "product.category_id" => "= '$dmi_category_id'",
  );
  foreach ($values as $field => $value) {
    if (!$value) continue;
    $products += $object->seek($value, $where, 50);
  }
  
  $reception = new CProductOrderItemReception;
  
  $where = array(
    "product.category_id" => "= '$dmi_category_id'",
    "product_order_item_reception.code != '' AND product_order_item_reception.code IS NOT NULL",
  );
  
  foreach ($products as $_product) {
    $where["product.product_id"] = "= '$_product->_id'";
    $_product->loadRefsFwd();
    $_product->_lots = $reception->loadList($where, null, null, null, $ljoin);
  }
}

foreach ($products as $_product) {
  foreach($_product->_lots as $_lot) {
    $_lot->_selected = isset($comp['lot']) && $comp['lot'] && ($_lot->code === $comp['lot']);
  }
  
  $_product->loadRefsBack();
  foreach($_product->_ref_references as $_reference) {
    $_reference->loadRefSociete();
  }
  
  $lot = new CProductOrderItemReception;
  $_product->_new_lot = $lot;
}

$societe = new CSociete;
$list_societes = $societe->loadList(null, "name");

$smarty = new CSmartyDP();
$smarty->assign("parsed", $parsed);
$smarty->assign("strict", $strict);
$smarty->assign("products", $products);
$smarty->assign("list_societes", $list_societes);
$smarty->display("inc_parse_barcode.tpl");
