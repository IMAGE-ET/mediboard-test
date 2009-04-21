<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Alexis Granger
 */

global $g;

$service_id = mbGetValueFromGetOrSession('service_id');
$patient_id = mbGetValueFromGet('patient_id');

// Services' stocks
$list_stocks_service = new CProductStockService();
$list_stocks_service->service_id = $service_id;
$list_stocks_service = $list_stocks_service->loadMatchingList();

// Dispensations list, calculated
$list_dispensations = array();

// List of the traces of deliveries corresponding to returns 
$list_returns = array();

$trace = new CProductDeliveryTrace();
$where = array(
  'service_id'    => "= $service_id",
  'quantity'      => "< 0",
);
$whereTrace = array(
  'quantity'      => "< 0",
  'date_delivery' => 'IS NULL'
);

foreach ($list_stocks_service as $stock) {
	$stock->loadRefsFwd();
	
  // We load the unique negative delivery for this [service - group stock]
  $stock_group = new CProductStockGroup();
  $stock_group->group_id = $g;
  $stock_group->product_id = $stock->product_id;
  $stock_group->loadMatchingObject();
  
	$ref = ($stock->order_threshold_optimum ? $stock->order_threshold_optimum : $stock->order_threshold_min);
	$dispensation = new CProductDelivery();
	$dispensation->service_id = $service_id;
	$dispensation->patient_id = $patient_id;
	$dispensation->quantity = ($stock->quantity < $ref) ? $ref - $stock->quantity : 0;
	$dispensation->stock_id = $stock_group->_id;
	$list_dispensations[$stock->_id] = $dispensation;
	
	$where['stock_id'] = "= '$stock_group->_id'";
	$delivery = new CProductDelivery();
	$delivery->loadObject($where);

	// And then the negative traces for this delivery 
	if ($delivery->_id) {
    $whereTrace['delivery_id'] = "= '$delivery->_id'";
    $list_returns[$stock->_id] = $trace->loadList($whereTrace);
	}
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks_service', $list_stocks_service);
$smarty->assign('list_returns', $list_returns);
$smarty->assign('list_dispensations',  $list_dispensations);

$smarty->display('inc_stocks_service_list.tpl');

?>