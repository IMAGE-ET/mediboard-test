<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can;
$can->needsRead();

// Calcul de date_max et date_min
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');
mbSetValueToSession('_date_min', $date_min);
mbSetValueToSession('_date_max', $date_max);

$order_by = 'date_dispensation DESC';
$where = array ();
$where[] = "date_dispensation BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where['quantity'] = " > 0";

$list_services = new CService();
$list_services = $list_services->loadGroupList();

$list_patients = array();

$deliveries_by_service = array();
$deliveries_by_patient = array();

foreach ($list_services as $service) {
  $where['service_id'] = " = $service->_id";
  $delivery = new CProductDelivery();
  $deliveries = $delivery->loadList($where, $order_by);
  
  foreach($deliveries as $_delivery){
    if (!$_delivery->isDelivered()) {
      $_delivery->loadRefsFwd();
      $_delivery->loadRefsBack();
      $_delivery->_ref_stock->loadRefsFwd();
    
      if($_delivery->patient_id){
        $_delivery->loadRefPatient();
        $list_patients[$_delivery->patient_id] = $_delivery->_ref_patient;
        
        if (!isset($deliveries_by_patient[$_delivery->patient_id]))
          $deliveries_by_patient[$_delivery->patient_id] = array();
        $deliveries_by_patient[$_delivery->patient_id][] = $_delivery;
      } 
      
      else {
        $_delivery->loadRefService();
        
        if (!isset($deliveries_by_service[$_delivery->service_id]))
          $deliveries_by_service[$_delivery->service_id] = array();
        $deliveries_by_service[$_delivery->service_id][] = $_delivery;
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('date_min', $date_min);
$smarty->assign('date_max', $date_max);

$smarty->assign('list_services', $list_services);
$smarty->assign('deliveries_by_service', $deliveries_by_service);

$smarty->assign('list_patients', $list_patients);
$smarty->assign('deliveries_by_patient', $deliveries_by_patient);

$smarty->display('print_prepare_plan.tpl');

?>