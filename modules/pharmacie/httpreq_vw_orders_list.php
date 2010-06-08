<?php /* $Id: httpreq_vw_deliveries_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$start = CValue::get('start', 0);

$service = new CService();
$list_services = $service->loadListWithPerms(PERM_READ);

$order_by = 'date_dispensation ASC';
$where = array (
 'order' => " = '1'",
 "date_delivery IS NULL OR date_delivery = ''"
);

$where['quantity'] = " > 0";
$delivery = new CProductDelivery();

$total = 0;
foreach($list_services as $_service) {
  $where["service_id"] = "= '$_service->_id'";
  
  $_service->_ref_deliveries = $delivery->loadList($where, $order_by/*, intval($start).",30"*/);
  $_service->_count_deliveries = $delivery->countList($where);
  $_service->_ref_delivery_stocks = array();
  
  $total += $_service->_count_deliveries;
  
  foreach($_service->_ref_deliveries as $_delivery) {
    if ($_delivery->_ref_stock->_id)
      $_service->_ref_delivery_stocks[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $_service->_id);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list_services', $list_services);
$smarty->assign('total', $total);
$smarty->assign('start', $start);
$smarty->display('inc_orders_list.tpl');

?>