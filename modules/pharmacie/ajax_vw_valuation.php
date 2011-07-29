<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$categorization = CValue::get("categorization");
$label          = CValue::get("label");
$list           = CValue::request("list");
$date           = CValue::post("date", mbDate());

if (!$date) {
  $date = mbDate("-0 MONTHS");
}

$date_first_log = "2011-01-01";

//CMbObject::$useObjectCache = false;
set_time_limit(300);
$limit = 3000;
$mode_delta = true;

$product_ids = is_string($list) ? explode(",", $list) : $list;

$product = new CProduct;
$where = array();

if (!empty($product_ids)) {
  $where["product_id"] = $product->_spec->ds->prepareIn($product_ids);
}
$list_products = $product->loadList($where, null, $limit);

$delivery = new CProductDeliveryTrace;
$reception = new CProductOrderItemReception;

CProductOrderItemReception::$_load_lite = true;
CProductOrderItem::$_load_lite = true;

$ds = $reception->_spec->ds;

$total = 0;
$total_ttc = 0;

$totals = array();

$ljoin_in = array(
  "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
  "product_order" => "product_order.order_id = product_order_item.order_id",
  "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
  "product" => "product_reference.product_id = product.product_id",
);

$ljoin_out = array(
  "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id",
  "product_stock_group" => "product_stock_group.stock_id = product_delivery.stock_id",
  "product" => "product.product_id = product_stock_group.product_id",
);

$biggest = array();

foreach($list_products as $_product) {
  $_product->loadRefStock();
  //mbTrace($_product->_view, " PRODUCT ", true);
  
  $total_product = 0;
  $total_product_ttc = 0;
  
  $quantity = $_product->_ref_stock_group->getValueAtDate($date, "quantity");
	$quantity_at_date = $quantity;
  //mbTrace($quantity, "qty at date", true);
  
  //$quantity = $_product->_ref_stock_group->quantity;
  //mbTrace($quantity, "qty 1", true);
  
  // rajout des délivrances et annulation des commandes jusqu'a la date voulue
  /*$req = new CRequest;
  $req->addTable($delivery->_spec->table);
  $req->addSelect(array(
    "SUM(product_delivery_trace.quantity) AS consumed_qty"
  ));
  $req->addLJoin($ljoin_out);
  //$req->addOrder("product_delivery_trace.date_delivery");
  
  $where = array(
    "product.product_id" => $ds->prepare("=%", $_product->_id),
    "product_delivery_trace.date_delivery" => $ds->prepare("> %", $date),
    "product_delivery.stock_class" => "= 'CProductStockGroup'",
  );
  $req->addWhere($where);
  
  $res = $req->getRequest();
  $list = $ds->loadHash($res);
  $consumed_qty = $list["consumed_qty"];
  
  $quantity += $consumed_qty;
  //mbTrace($consumed_qty, "consumed_qty", true);
  //mbTrace($quantity, "qty 2", true);
  
  // suppression des commandes recues entre temps
  $req = new CRequest;
  $req->addTable($reception->_spec->table);
  $req->addSelect(array(
    "SUM(product_order_item_reception.quantity) AS received_qty"
  ));
  $req->addLJoin($ljoin_in);
  
  $where = array(
    "product.product_id" => $ds->prepare("=%", $_product->_id),
    "product_order_item_reception.date" => $ds->prepare("> %", $date),
    "product_order.cancelled" => "='0'",
  );
  $req->addWhere($where);
  
  $res = $req->getRequest();
  $list = $ds->loadHash($res);
  $received_qty = $list["received_qty"];
  
  $quantity -= $received_qty;
  //mbTrace($received_qty, "received_qty", true);
  //mbTrace($quantity, "qty 3", true);*/
  
  // recuperation de la dernire commande avant la $date
  $order_item_reception = new CProductOrderItemReception;
  $where = array(
    "product.product_id" => $ds->prepare("=%", $_product->_id),
    "product_order_item_reception.date" => $ds->prepare("<= %", $date),
    "product_order.cancelled" => "='0'",
  );
  $list_oir = $order_item_reception->loadList($where, "product_order_item_reception.date DESC", null, null, $ljoin_in);
  
  //mbTrace($total, "total_before");
  $mean_price = null;
  
  foreach($list_oir as $_oir) {
    if ($quantity <= 0) {
      break;
    }
    
    $_oi = $_oir->loadRefOrderItem();
    
    //mbTrace($_oi->getPlainFields(), "dbf", true);
    //mbTrace($_oi, "oi", true);
    
    $qty = $_oir->quantity;
    $price = $_oi->unit_price;
    $_mean_price = $price;
    
    if (!isset($mean_price)) {
      $mean_price = $_mean_price;
    }
    
    $substract = min($qty, $quantity);
    
    //mbTrace($qty, "qty");
    //mbTrace($quantity, "quantity");
    
    $total_product += $substract * $_mean_price;
    $total_product_ttc += $substract * $_mean_price * (1 + $_oi->tva / 100);
    
    //mbTrace($qty, "qty", true);
    //mbTrace($quantity, "quantity", true);
    //mbTrace($substract, "substract", true);
  
    $quantity -= $substract;
  }
  
  //mbTrace($mean_price, '$mean_price', true);
  
  // s'il reste encore des articles pas comptabilisés, 
  // on ajoute leur valeur en fonction du premier prix d'achat
  if ($quantity > 0) {
    CProductReference::$_load_lite = true;
    $_refs = $_product->loadRefsReferences();
    $ref = reset($_refs);
    
    $old_ref_price = 0;
    $old_ref_tva = 0;
    if ($ref && $ref->_id) {
      $old_ref_price = $ref->getValueAtDate($date_first_log, "price");
      $old_ref_tva = $ref->getValueAtDate($date_first_log, "tva");
    }
    
    $total_product += $old_ref_price * $quantity;
    $total_product_ttc += $old_ref_price * $quantity * (1 + $old_ref_tva / 100);
  }
  
  if ($total_product > 1000) {
    $biggest[] = array($_product->_view, $total_product);
  }
  
  if ($total_product > 0) {
    $totals[$_product->_id] = array(
      "ht" => round($total_product, 2),
      "ttc" => round($total_product_ttc, 2),
			"quantity" => $quantity_at_date,
    );
    
    $total += $total_product;
    $total_ttc += $total_product_ttc;
  }
}

$result = array(
  "total" => round($total, 2),
  "total_ttc" => round($total_ttc, 2),
  "totals" => $totals,
);

//mbTrace($result); return;

CApp::json($result);

//echo round($total, 3);
