<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$product_id  = CValue::get('product_id');

$product = new CProduct();
$product = $product->load($product_id);

$product->loadRefStock();
$series = array(
  array("label" => utf8_encode("Entrées"), "color" => "#66CC00", "data" => array()),
  array("label" => utf8_encode("Sorties"), "color" => "#CB4B4B", "data" => array()),
);
$ticks = array();
$max = 1;

$now = mbDate();
$date = mbDate("-6 MONTHS");
$i = 0;

while($date < $now) {
  //$to = mbDate("+1 MONTH", $date);
  //$ticks[] = "Du ".mbDateToLocale($date)." au ".mbDateToLocale($to);
  
  $date = mbTransformTime(null, $date, "%Y-%m-01");
  $to = mbDate("+1 MONTH", $date);
  $ticks[] = array(count($ticks)*2-0.4, utf8_encode(mbTransformTime(null, $date, "%b")));
  
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
    $total += $_lot->getUnitQuantity();
  }
  $max = max($max, $total);
  
  $series[0]["data"][] = array(count($series[0]["data"])*2-0.8, $total);
  
  
  // Output //////////////////
  $where = array(
    "stock_id" => "= '{$product->_ref_stock_group->_id}'",
    "product_delivery_trace.date_delivery" => "BETWEEN '$date' AND '$to'",
  );
  
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
  ///////////////
  
  $date = $to;
}

$series = array_reverse($series);

$data = array(
  "series" => $series,
  "options" => CFlotrGraph::merge("bars", array(
    "bars" => array("barWidth" => 0.8),
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
$smarty->display('inc_product_consumption_graph.tpl');
