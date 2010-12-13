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

$list = array(500);  //1348

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
  // OUT
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
  mbTrace($consumed_qty, "out");
  
  // IN
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
  
  // si on a plus de recus que de consommés
  // on parcoure les receptions en partant de la plus 
  // ancienne et in decremente la différence pour arriver à 0
  if ($received_qty > $consumed_qty) {
    $delta = 60; //$received_qty - $consumed_qty;
    $copy_delta = $delta;
    
    $query = "SELECT 
    OIR1.order_item_reception_id, 
    OIR1.order_item_id, 
    OIR1.date,
    P1.`product_id`,
    OIR1.quantity as oirq, 
    R1.quantity as rq,
    P1.quantity as pq,
    OIR1.quantity * R1.quantity * P1.quantity AS qty,
    OI1.unit_price / R1.quantity as unit_price,
    OIR1.quantity * OI1.unit_price * P1.quantity AS price
    
    FROM product_order_item_reception OIR1
    
    LEFT JOIN `product_order_item` AS OI1 ON OI1.order_item_id = OIR1.order_item_id
    LEFT JOIN `product_reference` AS R1 ON R1.reference_id = OI1.reference_id
    LEFT JOIN `product` AS P1 ON R1.product_id = P1.product_id
    
    WHERE ".
    ($date ? "OIR1.date <= '$date' AND " : "").
    "P1.`product_id` = '{$_product->_id}' AND
    (
        SELECT 
        SUM(OIR2.quantity) * R2.quantity * P2.quantity
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
    
    //mbTrace($query);
    
    $list = $ds->loadList($query);
    mbTrace($list);
  }
  mbTrace($received_qty, "in");
}

/*
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);

$smarty->display('inc_vw_valuation.tpl');
*/