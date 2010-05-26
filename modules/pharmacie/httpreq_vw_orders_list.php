<?php /* $Id: httpreq_vw_deliveries_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$service_id = CValue::get('service_id');
$start      = CValue::get('start', 0);

$order_by = 'date_dispensation ASC';
$where = array (
 'order' => " = '1'",
 "date_delivery IS NULL OR date_delivery = ''"
);

$where['quantity'] = " > 0";
$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, intval($start).",30");
$total = $delivery->countList($where);

$stocks_service = array();
foreach($deliveries as $_delivery) {
  if ($_delivery->_ref_stock->_id)
    $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $service_id);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('deliveries', $deliveries);
$smarty->assign('stocks_service', $stocks_service);
$smarty->assign('total', $total);
$smarty->assign('start', $start);
$smarty->display('inc_orders_list.tpl');

?>