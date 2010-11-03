<?php /* $Id: httpreq_vw_deliveries_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$list_services = CProductStockGroup::getServicesList();

$delivery = new CProductDelivery();

$order = 'date_dispensation ASC';
$where["order"] = " = '1'";
$where['quantity'] = " > 0";
$where['service_id'] = CSQLDataSource::prepareIn(array_keys($list_services));
$where[] = "date_delivery IS NULL OR date_delivery = ''";

// Initialisation
$total = 0;
foreach($list_services as $_service) {
  $_service->_count_deliveries = 0;
  $_service->_ref_deliveries = array();
}

// Classement dans les services
$deliveries = $delivery->loadList($where, $order);
foreach ($deliveries as $_delivery) {
	$service =& $list_services[$_delivery->service_id];
	
	$service->_ref_deliveries[$_delivery->_id] = $_delivery;
  $service->_count_deliveries++;

	$stock = $_delivery->_ref_stock;
	if ($stock->_id) {
		$service_stock = CProductStockService::getFromCode($stock->_ref_product->code, $service->_id);
    $service->_ref_delivery_stocks[$_delivery->_id] = $service_stock;
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list_services', $list_services);
$smarty->assign('total', $total);
$smarty->display('inc_orders_list.tpl');

?>