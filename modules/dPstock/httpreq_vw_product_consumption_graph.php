<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$product_id = CValue::get('product_id');
$width      = CValue::get('width', 400);
$height     = CValue::get('height', 100);

$product = new CProduct();
$product = $product->load($product_id);

$product->loadRefStock();
$series = array(
  array("label" => utf8_encode("Entrées"), "color" => "#66CC00", "data" => array()),
  array("label" => utf8_encode("Sorties"), "color" => "#CB4B4B", "data" => array()),
  array("label" => utf8_encode("Périmés"), "color" => "#6600CC", "data" => array()),
);
$ticks = array();
$max = 1;

$now = CMbDT::date();
$date = CMbDT::date("-6 MONTHS");
$i = 0;

while($date < $now) {
  //$to = CMbDT::date("+1 MONTH", $date);
  //$ticks[] = "Du ".CMbDT::dateToLocale($date)." au ".CMbDT::dateToLocale($to);
  
  $date = CMbDT::transform(null, $date, "%Y-%m-01");
  $to = CMbDT::date("+1 MONTH", $date);
  $ticks[] = array(count($ticks)*2-0.4, utf8_encode(CMbDT::transform(null, $date, "%b")));
  
  // Input //////////////////
  $where = array(
    "product.product_id" => "= '{$product->_id}'",
    "product_order_item_reception.date" => "BETWEEN '$date' AND '$to'",
  );
  
  $ljoin = array(
    "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
    "product_reference" => "product_reference.reference_id = product_order_item.reference_id",
    "product" => "product.product_id = product_reference.product_id",
  );
  
  $lot = new CProductOrderItemReception;
  $lots = $lot->loadList($where, null, null, null, $ljoin);
  
  $total = 0;
  foreach($lots as $_lot) {
    $total += $_lot->quantity;
  }
  $max = max($max, $total);
  
  $series[0]["data"][] = array(count($series[0]["data"])*2-0.6, $total);
  
  // Hack pour les etablissements qui ont un service "Périmés"
  $where_services = array(
    "nom" => "= 'Périmés'",
  );
  $services_expired = new CService;
  $services_expired_ids = $services_expired->loadIds($where_services);
  
  // Output //////////////////
  $where = array(
    "product_delivery.stock_class"         => "= 'CProductStockGroup'",
    "product_delivery.stock_id"            => "= '{$product->_ref_stock_group->_id}'",
    "product_delivery_trace.date_delivery" => "BETWEEN '$date' AND '$to'",
  );
  
  if (count($services_expired_ids)) {
    $where[100] = "(product_delivery.type != 'expired' OR product_delivery.type IS NULL) AND product_delivery.service_id NOT IN (".implode(',', $services_expired_ids).")";
  }
  else {
    $where[100] =  "product_delivery.type != 'expired' OR product_delivery.type IS NULL";
  }
  
  $ljoin = array(
    "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id"
  );
  
  $trace = new CProductDeliveryTrace;
  $traces = $trace->loadList($where, null, null, null, $ljoin);
  
  $total = 0;
  foreach($traces as $_trace) {
    $total += $_trace->quantity;
  }
  $max = max($max, $total);
  $series[1]["data"][] = array(count($series[1]["data"])*2, $total);
  
  // Output expired ///////////////////
  if (count($services_expired_ids)) {
    $where[100] = "product_delivery.type = 'expired' OR product_delivery.service_id IN (".implode(',', $services_expired_ids).")";
  }
  else {
    $where[100] = "product_delivery.type = 'expired'";
  }
  
  $traces = $trace->loadList($where, null, null, null, $ljoin);
  
  $total_expired = 0;
  foreach($traces as $_trace) {
    $total_expired += $_trace->quantity;
  }
  $max = max($max, $total_expired + $total);
  $series[2]["data"][] = array(count($series[2]["data"])*2+0.6, $total_expired);
  ///////////////
  
  $date = $to;
}

$series = array_reverse($series);

$data = array(
  "series" => $series,
  "options" => CFlotrGraph::merge("bars", array(
    "fontSize" => 7,
    "shadowSize" => 0,
    "bars" => array("barWidth" => 0.6/*, "stacked" => true*/),
    "xaxis" => array(
      "showLabels" => true, 
      "ticks" => $ticks,
      "labelsAngle" => 0,
    ),
    "yaxis" => array(
      "showLabels" => false,
      "ticks" => array(array(0,  ""), array($max, "$max")),
      "max" => $max * 1.4, // 1.5 when markers
    ),
    "legend" => array("show" => false),
    //"mouse" => array("track" => true),
    "grid" => array("outlineWidth" => 1),
    "spreadsheet" => array("show" => false),
    "markers" => array("show" => true),
  ))
);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('data', $data);
$smarty->assign('product', $product);
$smarty->assign('width', $width);
$smarty->assign('height', $height);
$smarty->display('inc_product_consumption_graph.tpl');
