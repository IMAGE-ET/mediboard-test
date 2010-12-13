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
$date           = CValue::post("date", "2010-10-02");

$list = array(1348);

//CMbObject::$useObjectCache = false;
set_time_limit(300);
$limit = 1000;

$product_ids = is_string($list) ? explode(",", $list) : $list;

$product = new CProduct;
$where = array(
  "product_id" => $product->_spec->ds->prepareIn($product_ids)
);
$list_products = $product->loadList($where);

$delivery = new CProductDeliveryTrace;
$reception = new CProductOrderItemReception;
$ds = $reception->_spec->ds;

$ljoin_in = array(
  "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
  "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
  "product" => "product_reference.product_id = product.product_id",
);

$ljoin_out = array(
  "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id",
  "product_stock_group" => "product_stock_group.stock_id = product_delivery.stock_id",
  "product" => "product.product_id = product_stock_group.product_id",
);

foreach($list_products as $_product) {
  $_product->loadRefStock();

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
    "SUM(product_order_item_reception.quantity) * product_reference.quantity * product.quantity AS received_qty"
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
  
  // stock d'origine
  // recherche du premier log enregistré sur le stock de ce produit avant toute recepion ou delivrance
  $log = new CUserLog();
  $where = array(
    "user_log.object_class" => "= 'CProductStockGroup'",
    "user_log.object_id" => "= '{$_product->_ref_stock_group->_id}'",
    "user_log.date = product_order_item_reception.date OR 
     user_log.date = product_delivery_trace.date_delivery",
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
  $old_values = $log->getOldValues();
  $orig_quantity = $old_values["quantity"];
  
  // product.quantity au moment du log en question
  $old_product_quantity = $_product->getValueAtDate($log->date, "quantity");
  mbTrace($old_product_quantity, '$old_product_quantity');
  
  $ref = reset($_product->loadRefsReferences());
  if ($ref && $ref->_id) {
    $old_ref_price = $ref->getValueAtDate($log->date, "price");
    mbTrace($old_ref_price, '$old_ref_price');
  }
  
  ///// PRIX DE BASE //////////////////
  $base_value = $orig_quantity * $old_ref_price;
  
  $received_qty += $orig_quantity;
  
  // si on a plus de recus que de consommés
  // on parcoure les receptions en partant de la plus 
  // ancienne et in decremente la différence pour arriver à 0
  if ($received_qty > $consumed_qty) {
    $delta = $received_qty - $consumed_qty;
    
    mbTrace($received_qty, '$received_qty');
    mbTrace($consumed_qty, '$consumed_qty');
    
    $unit_order = CAppUI::conf("dPstock CProductStockGroup unit_order");
    
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
    OIR1.quantity * OI1.unit_price AS price
    
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
    foreach($list as $_item) {
      $remaining_value += $_item["qty"] * $_item["unit_price"];
      $delta -= $_item["qty"];
    }
    
    mbTrace($base_value, "base_value"); 
    mbTrace($remaining_value, "remaining_value");
    mbTrace($delta, "delta");
  }
}

/*
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);

$smarty->display('inc_vw_valuation.tpl');
*/