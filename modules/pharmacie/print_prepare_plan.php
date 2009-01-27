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

$date_min = "$date_min 00:00:00";
$date_max = "$date_max 23:59:59";

$order_by = 'date_dispensation DESC';
$where = array ();
$where[] = "date_dispensation BETWEEN '$date_min' AND '$date_max'";
$where['quantity'] = " > 0";
$where['patient_id'] = "IS NULL";

$list_services = new CService();
$list_services = $list_services->loadGroupList();

$deliveries = array();
foreach ($list_services as $service) {
  $where['service_id'] = " = $service->_id";
  $delivery = new CProductDelivery();
  $all_deliveries = $delivery->loadList($where, $order_by);
  
  foreach($all_deliveries as $_delivery){
    if (!$_delivery->isDelivered()) {
      $_delivery->loadRefsFwd();
      $_delivery->loadRefsBack();
      $_delivery->_ref_stock->loadRefsFwd();
      $_delivery->loadRefService();
      
      if (!isset($deliveries[$_delivery->service_id]))
        $deliveries[$_delivery->service_id] = array();
      $deliveries[$_delivery->service_id][] = $_delivery;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('date_min', $date_min);
$smarty->assign('date_max', $date_max);

$smarty->assign('list_services', $list_services);
$smarty->assign('deliveries', $deliveries);

$smarty->display('print_prepare_plan.tpl');

?>