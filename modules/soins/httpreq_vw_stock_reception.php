<?php /* $Id: httpreq_vw_restockages_service_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$service_id = CValue::getOrSession('service_id');
$mode       = CValue::get('mode');

$order_by = 'service_id, patient_id, date_dispensation DESC';
$where = array();
if ($service_id) {
  $where['service_id'] = " = $service_id";
}
$where['date_delivery'] = "IS NULL";
$where['quantity'] = " > 0";
$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, 30);

$deliveries_nominatif = array();
$deliveries_global = array();

// Creation d'un tableau de patient
$patients = array();
if (count($deliveries)) {
  foreach($deliveries as $_delivery){
    $_delivery->loadRefsFwd();
    $_delivery->loadRefsBack();
    $_delivery->_ref_stock->loadRefsFwd();
    $_delivery->loadRefPatient();
    $_delivery->loadRefService();
    
    /*if($_delivery->patient_id){
      $_delivery->loadRefPatient();
      $deliveries_nominatif[$_delivery->_id] = $_delivery;
    } else {
      $_delivery->loadRefService();
      $deliveries_global[$_delivery->_id] = $_delivery;
    }*/
    $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $service_id);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('deliveries', $deliveries);
if (!$mode) {
  $smarty->display('inc_stock_reception.tpl');
}
else {
  $smarty->display('print_stock_reception.tpl');
}
?>