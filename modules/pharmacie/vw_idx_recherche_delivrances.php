<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$datetime_min = CValue::get("_datetime_min", CValue::getOrSession("_datetime_recherche_min", mbDateTime(mbDate())));
$datetime_max = CValue::get("_datetime_max", CValue::getOrSession("_datetime_recherche_max", mbDateTime("+1 DAY -1 SECOND", mbDate())));
$service_id   = CValue::get("service_id", CValue::getOrSession("service_id_recherche"));
$delivery_trace_id = CValue::getOrSession("delivery_trace_id");

CValue::setSession("_datetime_recherche_min", $datetime_min);
CValue::setSession("_datetime_recherche_max", $datetime_max);
CValue::setSession("service_id_recherche", $service_id);

$delivery = new CProductDelivery;
$delivery->_datetime_min = $datetime_min;
$delivery->_datetime_max = $datetime_max;
$delivery->service_id = $service_id;

$delivery_trace = new CProductDeliveryTrace;
$delivery_trace->delivery_trace_id = $delivery_trace_id;

$where = array(
  "product_delivery_trace.date_delivery" => "BETWEEN '$datetime_min' AND '$datetime_max'",
);

if ($service_id) {
  $where["product_delivery.service_id"] = "= '$service_id'";
}

$ljoin = array(
  "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id"
);
$delivery_traces = $delivery_trace->loadList($where, "product_delivery_trace.date_delivery", 1000, null, $ljoin);

foreach($delivery_traces as $_trace) {
	$_trace->loadRefDelivery()->loadRefStock();
}

$service = new CService;
$services = $service->loadListWithPerms(PERM_READ);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("delivery", $delivery);
$smarty->assign("delivery_trace", $delivery_trace);
$smarty->assign("delivery_traces", $delivery_traces);
$smarty->assign("services", $services);
$smarty->display("vw_idx_recherche_delivrances.tpl");
