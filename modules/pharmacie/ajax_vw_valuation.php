<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$categorization = CValue::get("categorization");
$label          = CValue::get("label");
$list           = CValue::post("list");
$date           = CValue::post("date", mbDate());

if (!$date) {
  $date = mbDate("-0 MONTHS");
}

$date_first_log = "2010-06-20";

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
$ds = $reception->_spec->ds;

$total = 0;
$total_ttc = 0;

$totals = array();

function getFirstQuantityLog($product, $date_first_log = null) {
  if ($date_first_log) {
    $orig_quantity = $_product->_ref_stock_group->getValueAtDate($date_first_log, "quantity");
    $old_product_quantity = $_product->getValueAtDate($date_first_log, "quantity");
    $old_ref_quantity = 1;
    
    CProductReference::$_load_lite = true;
    $ref = reset($_product->loadRefsReferences());
    
    $old_ref_price = 0;
    $old_ref_tva = 0;
    if ($ref && $ref->_id) {
      $old_ref_price = $ref->getValueAtDate($date_first_log, "price");
      $old_ref_tva = $ref->getValueAtDate($date_first_log, "tva");
      $old_ref_quantity = $ref->getValueAtDate($date_first_log, "quantity");
    }
  }
  else {
    // stock d'origine
    // recherche du premier log enregistré sur le stock de ce produit avant toute recepion ou delivrance
    $log = new CUserLog();
    $where = array(
      "user_log.object_class" => "= 'CProductStockGroup'",
      "user_log.object_id" => "= '{$product->_ref_stock_group->_id}'",
      "user_log.date = product_order_item_reception.date OR 
       user_log.date = product_delivery_trace.date_delivery",
      "user_log.extra" => "IS NOT NULL",
      "user_log.type" => "IN('store', 'merge')",
      "product_delivery.stock_class" => "= 'CProductStockGroup'",
    );
    $ljoin = array(
      "product_stock_group" => "user_log.object_id = product_stock_group.stock_id",
      "product_reference" => "product_reference.product_id = product_stock_group.product_id",
      "product_order_item" => "product_reference.reference_id = product_order_item.reference_id",
      "product_order_item_reception" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
    
      "product_delivery" => "product_stock_group.stock_id = product_delivery.stock_id",
      "product_delivery_trace" => "product_delivery.delivery_id = product_delivery_trace.delivery_id",
    );
    $order = null;// "product_order_item_reception.date ASC, product_delivery_trace.date_delivery ASC"; // takes a LOOONNG time
    
    $log->loadObject($where, $order, null, $ljoin);
    
    if ($log->_id) {
      $old_values = $log->getOldValues();
      $orig_quantity = $old_values["quantity"];
    }
    else {
      $orig_quantity = $product->_ref_stock_group->quantity;
    }
    
    // product.quantity au moment du log en question
    $old_product_quantity = $product->getValueAtDate($log->date, "quantity");
    $old_ref_quantity = 1;
  
    $ref = reset($product->loadRefsReferences());
    $old_ref_price = 0;
    if ($ref && $ref->_id) {
      $old_ref_price = $ref->getValueAtDate($log->date, "price");
      $old_ref_tva = $ref->getValueAtDate($date_first_log, "tva");
      $old_ref_quantity = $ref->getValueAtDate($date_first_log, "quantity");
    }
  }
  
  return array(
    $old_ref_price, 
    $old_ref_tva, 
    $old_ref_quantity,
    
    "price"    => $old_ref_price,
    "tva"      => $old_ref_tva,
    "quantity" => $old_ref_quantity,
  );
}

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

$unit_order = false && CAppUI::conf("dPstock CProductStockGroup unit_order");

$biggest = array();

foreach($list_products as $_product) {
  $_product->loadRefStock();
  //mbTrace($_product->_view, " PRODUCT ", true);
  
  $total_product = 0;
  $total_product_ttc = 0;
  
  $quantity = $_product->_ref_stock_group->quantity;
  //mbTrace($quantity, "qty 1");
  
  // rajout des délivrances et annulation des commandes jusqu'a la date voulue
  $req = new CRequest;
  $req->addTable($delivery->_spec->table);
  $req->addSelect(array(
    "SUM(product_delivery_trace.quantity) AS consumed_qty"
  ));
  $req->addLJoin($ljoin_out);
  $req->addOrder("product_delivery_trace.date_delivery");
  
  $where = array(
    "product.product_id" => $ds->prepare("=%", $_product->_id),
    "product_delivery.stock_class" => "= 'CProductStockGroup'",
  );
  
  if ($date) {
    $where["product_delivery_trace.date_delivery"] = $ds->prepare("> %", $date);
  }
  $req->addWhere($where);
  
  $res = $req->getRequest();
  $list = $ds->loadHash($res);
  $consumed_qty = $list["consumed_qty"];
  
  $quantity += $consumed_qty;
  //mbTrace($quantity, "qty 2");
  
  // suppression des commandes recues entre temps
  $req = new CRequest;
  $req->addTable($reception->_spec->table);
  $req->addSelect(array(
    "SUM(product_order_item_reception.quantity) * product_reference.quantity ".($unit_order ? "" : " * product.quantity")." AS received_qty"
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
  //mbTrace($quantity, "qty 3");
  
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
    if ($quantity < 0) {
      //mbTrace("Valeur négative!", $quantity, true);
      break;
    }
    
    if ($quantity == 0) break;
    
    $_oi = $_oir->loadRefOrderItem();
    $_oi->loadReference();
    $_ref_quantity = $_oi->_ref_reference->quantity;
    
    $qty = $_oir->quantity * $_ref_quantity;
    $price = $_oi->unit_price * $qty;
    $mean_price = $price / ($qty * $_product->quantity);
    
    $substract = min($qty, $quantity);
    
    //mbTrace($qty, "qty");
    //mbTrace($quantity, "quantity");
    
    $total_product += $substract * $mean_price;
    $total_product_ttc += $substract * $mean_price * (1 + $_oi->tva / 100);
    
    /*mbTrace($qty, "qty", true);
    mbTrace($quantity, "quantity", true);
    mbTrace($substract, "substract", true);*/
  
    $quantity -= $substract;
  }
  
  // s'il reste encore des articles pas comptabilisés, 
  // on ajoute leur valeur en fonction du premier prix d'achat
  if ($quantity > 0) {
    if (!isset($mean_price)) {
      CProductReference::$_load_lite = true;
      $ref = reset($_product->loadRefsReferences());
      
      $old_ref_quantity = 1;
      $old_ref_price = 0;
      $old_ref_tva = 0;
      if ($ref && $ref->_id) {
        $old_ref_price = $ref->getValueAtDate($date_first_log, "price");
        $old_ref_tva = $ref->getValueAtDate($date_first_log, "tva");
        $old_ref_quantity = $ref->getValueAtDate($date_first_log, "quantity");
      }
      
      $mean_price = $old_ref_price / ($old_ref_quantity * $_product->quantity);
      $tva = $old_ref_tva;
    }
    else {
      $tva = $_oi->tva;
    }
    
    //mbTrace($quantity, "quantity", true);
    
    $total_product += $mean_price * $quantity;
    $total_product_ttc += $mean_price * $quantity * (1 + $tva / 100);
  }
  
  if ($total_product > 1000) {
    $biggest[] = array($_product->_view, $total_product);
  }
  
  if ($total_product > 0) {
    $totals[$_product->_id] = array(
      "ht" => round($total_product, 2),
      "ttc" => round($total_product_ttc, 2),
    );
    
    $total += $total_product;
    $total_ttc += $total_product_ttc;
  }
}

//mbTrace($biggest);

if (false)
foreach($list_products as $_product) {
  //mbTrace($_product->_view, " PRODUCT ");
  
  $_product->loadRefStock();
  
  if ($mode_delta) {
    // MODE 1 calcul du stock theorique en fonction des entrées - sorties + stock d'origine
    // comptage des sorties
    $req = new CRequest;
    $req->addTable($delivery->_spec->table);
    $req->addSelect(array(
      "SUM(product_delivery_trace.quantity) AS consumed_qty"
    ));
    $req->addLJoin($ljoin_out);
    $req->addOrder("product_delivery_trace.date_delivery");
    
    $where = array(
      "product.product_id" => $ds->prepare("=%", $_product->_id),
      "product_delivery.stock_class" => "= 'CProductStockGroup'",
    );
    if ($date) {
      $where["product_delivery_trace.date_delivery"] = $ds->prepare("<= %", $date);
    }
    $req->addWhere($where);
    
    $res = $req->getRequest();
    $list = $ds->loadHash($res);
    $consumed_qty = $list["consumed_qty"];
    
    // comptage des entrées
    $req = new CRequest;
    $req->addTable($reception->_spec->table);
    $req->addSelect(array(
      "SUM(product_order_item_reception.quantity) * product_reference.quantity ".($unit_order ? "" : " * product.quantity")." AS received_qty"
    ));
    $req->addLJoin($ljoin_in);
    $req->addOrder("product_order_item_reception.date");
    
    $where = array(
      "product.product_id" => $ds->prepare("=%", $_product->_id),
    );
    if ($date) {
      $where["product_order_item_reception.date"] = $ds->prepare("<= %", $date);
    }
    $req->addWhere($where);
    
    $res = $req->getRequest();
    $list = $ds->loadHash($res);
    $received_qty = $list["received_qty"];
    
    list($old_ref_price, $old_ref_tva, $old_ref_quantity) = getFirstQuantityLog($_product, $date_first_log);
    
    $received_qty += ($orig_quantity * $old_product_quantity * $old_ref_quantity); 
    
    $delta = $received_qty - $consumed_qty;
    
    /*mbTrace($received_qty, '# quantité reçue');
    mbTrace($consumed_qty, '# quantité consommée');
    
    mbTrace($delta, "stock theorique");
    mbTrace($_product->_ref_stock_group->quantity, "stock reel");*/
  }
  else {
    // MODE 2 : stock reellement enregistré dans MB
    $delta = $_product->_ref_stock_group->quantity;
  }
  
  ///// PRIX DE BASE //////////////////
  $base_value = $orig_quantity * $old_ref_price;
  //mbTrace($base_value, '# prix d\'origine');
  
  //mbTrace($delta, "delta");
  
  // si on a plus de recus que de consommés
  // on parcoure les receptions en partant de la plus 
  // ancienne et on decremente la différence pour arriver à 0
  if ($delta > 0) {
    
    $query = "SELECT 
    OIR1.order_item_reception_id, 
    OIR1.order_item_id, 
    OIR1.date,
    P1.`product_id`,
    OIR1.quantity as oirq, 
    R1.quantity as rq,
    P1.quantity as pq,
    OIR1.quantity * R1.quantity ".($unit_order ? "" : " * P1.quantity")." AS qty,
    OI1.unit_price / R1.quantity as unit_price,
    OIR1.quantity * OI1.unit_price AS price,
    OI1.tva AS tva
    
    FROM product_order_item_reception OIR1
    
    LEFT JOIN `product_order_item` AS OI1 ON OI1.order_item_id = OIR1.order_item_id
    LEFT JOIN `product_reference` AS R1 ON R1.reference_id = OI1.reference_id
    LEFT JOIN `product` AS P1 ON R1.product_id = P1.product_id
    
    WHERE ".
    ($date ? "OIR1.date <= '$date' AND " : "").
    "P1.`product_id` = '{$_product->_id}' AND
    (
        SELECT 
        SUM(OIR2.quantity) * R2.quantity ".($unit_order ? "" : " * P2.quantity")."
        FROM product_order_item_reception OIR2
        
        LEFT JOIN `product_order_item` AS OI2 ON OI2.order_item_id = OIR2.order_item_id
        LEFT JOIN `product_reference` AS R2 ON R2.reference_id = OI2.reference_id
        LEFT JOIN `product` AS P2 ON R2.product_id = P2.product_id
        
        WHERE 
        P2.product_id = '{$_product->_id}' AND 
        OIR2.date <= OIR1.date
        
        ORDER BY OIR2.date DESC
    ) <= $delta
    
    ORDER BY OIR1.date ASC";
    
    $list = $ds->loadList($query);
    
    $remaining_value = 0;
    $remaining_value_ttc = 0;
    foreach($list as $_item) {
      $remaining_value += $_item["qty"] * $_item["unit_price"];
      $remaining_value_ttc += $_item["qty"] * $_item["unit_price"] * (1 + $_item["tva"] / 100);
      $delta -= $_item["qty"];
    }
    
    if ($delta) {
      $last_price = end($list);
      $last_price = $last_price["unit_price"];
      $tva = $last_price["tva"];
      $remaining_value += $delta * $last_price;
      $remaining_value_ttc += $delta * $last_price * (1 + $tva / 100);
    }
    
    //mbTrace($delta, "remaining_delta");
    //mbTrace($remaining_value, "remaining_value");
    
    $total += $remaining_value;
    $total_ttc += $remaining_value_ttc;
  }
  elseif($mode_delta) {
    $total += $delta * $old_ref_price;
    $total_ttc += $delta * $old_ref_price * (1 + $old_ref_tva / 100);
  }
}

CApp::json(array(
  "total" => round($total, 2),
  "total_ttc" => round($total_ttc, 2),
  "totals" => $totals,
));

//echo round($total, 3);
