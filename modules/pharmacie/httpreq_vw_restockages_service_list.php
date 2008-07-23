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
$received = mbGetValueFromGetOrSession('received') == 'true';

// Calcul de date_max et date_min
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');

$order_by = 'date_delivery DESC';
$where = array ();
if ($service_id) {
  $where['service_id'] = " = $service_id";
}
$where['date_reception'] = $received ? 'IS NOT NULL' : 'IS NULL';
$where[] = "date_delivery BETWEEN '$date_min' AND '$date_max'";
$delivery = new CProductDelivery();
$list_deliveries = $delivery->loadList($where, $order_by, 20);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list_deliveries', $list_deliveries);
$smarty->display('inc_restockages_service_list.tpl');

?>