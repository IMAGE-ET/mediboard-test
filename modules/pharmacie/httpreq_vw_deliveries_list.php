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

$where['service_id'] = CSQLDataSource::prepareIn(array_keys($services));
$where[] = "date_dispensation BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where['quantity'] = " > 0";
//$where[] = "`order` != '1' OR `order` IS NULL";

$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, 200);

$stocks_service = array();

// Creation d'un tableau de patient
if (count($deliveries)) {
  foreach($deliveries as $_delivery){
  	//if ($display_delivered || !$_delivery->isDelivered()) {
	    $_delivery->loadRefsFwd();
	    $_delivery->loadRefsBack();
	    $_delivery->_ref_stock->loadRefsFwd();
      
      $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $_delivery->service_id);
    //}
  }
}

$service_keys = array_keys($services);
$deliveries_by_service = array_fill_keys($service_keys, array());
$delivered_counts = array_fill_keys($service_keys, 0);

foreach ($deliveries as $_delivery) {
  $service_id = $_delivery->service_id;
  $deliveries_by_service[$service_id][$_delivery->_id] = $_delivery;
        
  if ($_delivery->isDelivered()) {
    $delivered_counts[$service_id]++;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('deliveries',     $deliveries);
$smarty->assign('deliveries_by_service', $deliveries_by_service);
$smarty->assign('stocks_service', $stocks_service);
$smarty->assign('services',       $services);
$smarty->assign('delivered_counts', $delivered_counts);

if ($mode == "nominatif")
  $smarty->display('inc_deliveries_nominatif_list.tpl');
else
  $smarty->display('inc_deliveries_global_list.tpl');

?>