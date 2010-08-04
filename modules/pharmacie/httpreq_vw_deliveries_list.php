<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$mode               = CValue::getOrSession('mode');
$display_delivered  = CValue::getOrSession('display_delivered', 'false') == 'true';

$order_col = CValue::get('order_col', 'date_dispensation');
$order_way = CValue::get('order_way', 'DESC');
CValue::setSession('order_col', $order_col);
CValue::setSession('order_way', $order_way);

// Calcul de date_max et date_min
$date_min = CValue::getOrSession('_date_min');
$date_max = CValue::getOrSession('_date_max');
CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

if (!in_array($mode, array("global", "nominatif"))) {
  $mode = "global";
}

$service = new CService;
$services = $service->loadGroupList();
$services["none"] = new CService;
$services["none"]->_view = "";
$service_keys = array_keys($services);

$deliveries_by_service = array_fill_keys($service_keys, array());
$delivered_counts = array_fill_keys($service_keys, 0);
$order_by_product = false;
$stocks_service = array();

$col = $order_col;
switch($col) {
  case "product_id":  $col = "product.name"; break;
  case "location_id": $col = "product_stock_location.position"; break;
}

$order_by = "product_delivery.service_id, product_delivery.patient_id, $col $order_way";

$where = array();
if ($mode == "global")
  $where['product_delivery.patient_id'] = "IS NULL";
else
  $where['product_delivery.patient_id'] = "IS NOT NULL";
  
$where[] = "product_delivery.date_dispensation BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where['product_delivery.quantity'] = " > 0";
$where['product_delivery.stock_id'] = "IS NOT NULL";
//$where[] = "`order` != '1' OR `order` IS NULL";

if (!$display_delivered) {
  $where[] = "date_delivery IS NULL OR date_delivery = ''";
}

$ljoin = array(
  "product_stock_group" => "product_stock_group.stock_id = product_delivery.stock_id",
  "product_stock_location" => "product_stock_location.stock_location_id = product_stock_group.location_id",
  "product" => "product.product_id = product_stock_group.product_id",
);
  
$delivery = new CProductDelivery();

foreach($services as $_service_id => $_service) {
  if ($_service_id == "none")
    $where['service_id'] = "IS NULL OR service_id = ''";
  else
    $where['service_id'] = " = '$_service->_id'";
  
  $deliveries = $delivery->loadList($where, $order_by, 200, null, $ljoin);
  
  foreach($deliveries as $_id => $_delivery) {
    $_delivery->loadRefsFwd();
    $_delivery->_ref_stock->loadRefsFwd();
    $_delivery->isDelivered();
    
    $delivered = /*$_delivery->date_delivery || */$_delivery->_delivered;
    if (!$delivered || ($delivered && $display_delivered)) {
      // can't touch this !
    }
    else {
      unset($deliveries[$_id]);
    }
  }
  
  foreach($deliveries as $_id => $_delivery) {
    $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $_delivery->service_id);

    /*if ($order_by_product) {
      $key = str_pad($_delivery->_ref_stock->_ref_product->name, 50, " ", STR_PAD_RIGHT).$_delivery->date_dispensation;
    }
    else {
      $key = str_pad(mbMinutesRelative($_delivery->date_dispensation, mbDateTime())+1, 20, " ", STR_PAD_LEFT).
             str_pad($_delivery->_ref_stock->_ref_product->name, 50, " ", STR_PAD_RIGHT).
             $_delivery->_id;
    }*/
    $key = $_delivery->_id;
    
    if ($_delivery->date_delivery || $_delivery->_delivered) {
      $delivered_counts[$_service_id]++;
    }
    
    $deliveries_by_service[$_service_id][] = $_delivery;
  }
  
  /*foreach($deliveries_by_service as &$_list) {
    ksort($_list);
  }*/
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('order_col',     $order_col);
$smarty->assign('order_way',     $order_way);
$smarty->assign('deliveries',     $deliveries);
$smarty->assign('deliveries_by_service', $deliveries_by_service);
$smarty->assign('stocks_service', $stocks_service);
$smarty->assign('services',       $services);
$smarty->assign('delivered_counts', $delivered_counts);
$smarty->assign('display_delivered', $display_delivered);

if ($mode == "nominatif")
  $smarty->display('inc_deliveries_nominatif_list.tpl');
else
  $smarty->display('inc_deliveries_global_list.tpl');

?>