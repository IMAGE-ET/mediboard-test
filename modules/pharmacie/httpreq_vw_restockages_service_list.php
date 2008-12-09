<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');
$mode       = mbGetValueFromGetOrSession('mode');

// Calcul de date_max et date_min
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');
mbSetValueToSession('_date_min', $date_min);
mbSetValueToSession('_date_max', $date_max);

$order_by = 'date_dispensation DESC';
$where = array ();
if ($service_id) {
  $where['service_id'] = " = $service_id";
}
$where[] = "date_dispensation BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where['quantity'] = " > 0";
$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, 20);

$deliveries_nominatif = array();
$deliveries_global = array();

// Creation d'un tableau de patient
$patients = array();
if (count($deliveries)) {
  foreach($deliveries as $_delivery){
    $_delivery->loadRefsFwd();
    $_delivery->loadRefsBack();
    $_delivery->_ref_stock->loadRefsFwd();
    
    if($_delivery->patient_id){
      $_delivery->loadRefPatient();
      $deliveries_nominatif[$_delivery->_id] = $_delivery;
    } else {
      $_delivery->loadRefService();
      $deliveries_global[$_delivery->_id] = $_delivery;
    }
    $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $service_id);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('mode',    $mode);
$smarty->assign('deliveries_global', $deliveries_global);
$smarty->assign('deliveries_nominatif', $deliveries_nominatif);

$smarty->display('inc_restockages_service_list.tpl');

?>