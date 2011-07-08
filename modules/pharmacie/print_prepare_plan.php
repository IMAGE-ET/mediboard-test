<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Calcul de date_max et date_min
$mode       = CValue::get("mode");
$service_selection = CValue::get("service_id");

$datetime_min = CValue::getOrSession('_datetime_min');
$datetime_max = CValue::getOrSession('_datetime_max');

$order_by = 'product_stock_location.position ASC, patients.nom ASC, product.name ASC';

$where = array(
  'product_delivery.quantity' => " > 0",
  'product_delivery.stock_class' => " = 'CProductStockGroup'",
  'product_delivery.order = \'0\' OR product_delivery.order IS NULL',
  'product_delivery.date_delivery IS NULL',
);

$where['product_delivery.datetime_min'] = " < '$datetime_max'";
$where['product_delivery.datetime_max'] = " > '$datetime_min'";

if($mode == "nominatif"){
  $where["product_delivery.patient_id"] = " IS NOT NULL";
} else {
  $where["product_delivery.patient_id"] = " IS NULL";
}

$ljoin = array(
  'product_stock_group' => 'product_stock_group.stock_id = product_delivery.stock_id',
  'product_stock_location' => 'product_stock_location.stock_location_id = product_stock_group.location_id',
  'product' => 'product.product_id = product_stock_group.product_id',
	'patients' => 'product_delivery.patient_id = patients.patient_id'
);

$list_services = CProductStockGroup::getServicesList();

if (count($service_selection) > 0) {
  foreach($list_services as $_key => $_service) {
    if (!in_array($_key, $service_selection)) {
      unset($list_services[$_key]);
    }
  }
}

$deliveries = array();
foreach ($list_services as $service) {
  $where['service_id'] = " = '$service->_id'";
  $delivery = new CProductDelivery();
	
	$where['product_stock_location.position'] = "IS NOT NULL";
	$all_deliveries_located = $delivery->loadList($where, $order_by, null, null, $ljoin);
	
	$where['product_stock_location.position'] = "IS NULL";
  $all_deliveries_not_located = $delivery->loadList($where, $order_by, null, null, $ljoin);

  $all_deliveries = array_merge($all_deliveries_located, $all_deliveries_not_located);

  foreach($all_deliveries as $_delivery){
    if (!$_delivery->isDelivered()) {
      $_delivery->loadRefsFwd();
      $_delivery->loadRefsBack();
      $_delivery->_ref_stock->loadRefsFwd();
      $_delivery->loadRefService();
      
      if (!isset($deliveries[$_delivery->service_id])){
        $deliveries[$_delivery->service_id] = array();
			}
      $deliveries[$_delivery->service_id][] = $_delivery;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('date_min'     , $datetime_min);
$smarty->assign('date_max'     , $datetime_max);
$smarty->assign('list_services', $list_services);
$smarty->assign('deliveries'   , $deliveries);
$smarty->assign('mode'         , $mode);
$smarty->display('print_prepare_plan.tpl');

?>