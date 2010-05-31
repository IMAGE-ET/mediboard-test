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
$series = array();
$ticks = array();
$max = 1;

for($i = 6; $i > 0; $i--) {
  $from = mbDate("-$i MONTHS");
  $to   = mbDate("-$i MONTHS +1 MONTH");
  
  $where = array(
    "stock_id" => "= '{$product->_ref_stock_group->_id}'",
    "product_delivery_trace.date_delivery" => "BETWEEN '$from' AND '$to'",
  );
  
  $ljoin = array(
    "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_trace_id"
  );
  
  $trace = new CProductDeliveryTrace;
  $traces = $trace->loadList($where, null, null, null, $ljoin);
  
  $total = 0;
  foreach($traces as $_trace) {
    $total += $_trace->quantity;
  }
  
  $max = max($max, $total);
  $ticks[] = "$i mois";
  $series[] = array(count($series), $total);
}

$data = array(
  "series" => array($series),
  "options" => CFlotrGraph::merge("lines", array(
    "xaxis" => array("showLabels" => false, "ticks" => $ticks),
    "yaxis" => array(
      "ticks" => array(0 => "", $max => $max),
      "max" => $max * 1.5,
    ),
    "grid" => array("outlineWidth" => 1),
    "spreadsheet" => array("show" => false),
    "points" => array("show" => false),
  ))
);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('data', $data);
$smarty->assign('product', $product);
$smarty->display('inc_product_consumption_graph.tpl');
