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

$order_by = 'service_id, patient_id, date_dispensation DESC';
$where = array();

if ($mode == "global")
  $where['patient_id'] = "IS NULL";
else
  $where['patient_id'] = "IS NOT NULL";

$where[] = 'service_id '.CSQLDataSource::prepareIn(array_keys($services))." OR service_id IS NULL OR service_id = ''";
$where[] = "date_dispensation BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where['quantity'] = " > 0";
$where['stock_id'] = "IS NOT NULL";
//$where[] = "`order` != '1' OR `order` IS NULL";

if (!$display_delivered) {
  $where['date_delivery'] = "IS NULL";
}

$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, 200);

foreach($deliveries as $_id => $_delivery) {
  $delivered = $_delivery->date_delivery || $_delivery->isDelivered();
  if (!$delivered || ($delivered && $display_delivered)) {
    // can't touch this !
  }
  else {
    unset($deliveries[$_id]);
  }
}

$stocks_service = array();
foreach($deliveries as $_delivery){
  $_delivery->loadRefsFwd();
  $_delivery->loadRefsBack();
  $_delivery->_ref_stock->loadRefsFwd();
  
  $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $_delivery->service_id);
}

$services["none"] = new CService;
$services["none"]->_view = "";

$service_keys = array_keys($services);
$deliveries_by_service = array_fill_keys($service_keys, array());
$delivered_counts = array_fill_keys($service_keys, 0);

$order_by_product = false;

foreach ($deliveries as $_delivery) {
  $service_id = $_delivery->service_id ? $_delivery->service_id : 'none';
  
  if ($order_by_product) {
    $key = str_pad($_delivery->_ref_stock->_ref_product->_view, 50, " ", STR_PAD_RIGHT).$_delivery->date_dispensation;
  }
  else {
    $key = str_pad(mbMinutesRelative($_delivery->date_dispensation, mbDateTime())+1, 20, " ", STR_PAD_LEFT).
           str_pad($_delivery->_ref_stock->_ref_product->_view, 50, " ", STR_PAD_RIGHT).
           $_delivery->_id;
  }
  
  if (/*$_delivery->date_delivery || */$_delivery->isDelivered()) {
    $delivered_counts[$service_id]++;
  }
  
  $deliveries_by_service[$service_id][$key] = $_delivery;
}

foreach($deliveries_by_service as &$_list) {
  ksort($_list);
}

// Création du template
$smarty = new CSmartyDP();

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