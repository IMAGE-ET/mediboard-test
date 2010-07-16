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

$products = array();
$lots = array();

$lot = isset($comp['lot']) ? $comp['lot'] : trim($comp['raw'], ".%\n\r\t +-");

$object = new CProductOrderItemReception;
$where = array(
  "(lapsing_date > '".mbDate()."' OR lapsing_date IS NULL)",
  "(code != '' AND code IS NOT NULL)",
);
$lots = $object->seek($lot, $where, 50);

foreach($lots as $_lot) {
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
  
  foreach ($values as $field=>$value) {
    $products += $object->seek($value, null, 50);
  }
  
  $reception = new CProductOrderItemReception;
  $ljoin = array(
    "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
    "product_reference" => "product_order_item.reference_id = product_reference.reference_id",
  );
  
  foreach ($products as $_product) {
    $_product->loadRefsFwd();
    
    $where = array(
      "product_reference.product_id"=>"= '$_product->_id'",
      "product_order_item_reception.code != '' AND product_order_item_reception.code IS NOT NULL",
    );
    
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

$smarty = new CSmartyDP();
$smarty->assign("parsed", $parsed);
$smarty->assign("products", $products);
$smarty->display("inc_parse_barcode.tpl");
