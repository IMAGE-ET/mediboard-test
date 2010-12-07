<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$type      = CValue::get("type");
$_date_max = CValue::get("_date_max");
$_date_min = /*CValue::get("_date_min", */mbDate("-1 YEAR", $_date_max)/*)*/;
$keywords  = CValue::get("keywords");

CValue::setSession("type", $type);
CValue::setSession("_date_min", $_date_min);
CValue::setSession("_date_max", $_date_max);
CValue::setSession("keywords", $keywords);

//CMbObject::$useObjectCache = false;
set_time_limit(300);
$limit = 1000;

$delivery = new CProductDelivery; // CProductDeliveryTrace has dateTime specs (not CProductDelivery)
$delivery->type = $type;
$delivery->_date_min = $_date_min;
$delivery->_date_max = $_date_max;

$select = array(
  "product_delivery.service_id", 
  "DATE_FORMAT(product_delivery_trace.date_delivery, '%Y-%m') AS date", 
  "product_delivery.type AS type", 
  "SUM(product_delivery_trace.quantity) AS total",
);

$where = array(
  "product_delivery_trace.date_delivery" => "BETWEEN '$_date_min' AND '$_date_max'",
);

if ($type) {
  $where["product_delivery.type"] = "='$type'";
}

if ($keywords) {
  $split = explode(" ", $keywords);
  $whereOr = array();
  foreach($split as $_word) {
    $whereOr[] = "product_delivery.comments ".$delivery->_spec->ds->prepareLike("%$_word%");
  }
  $where[] = implode(" OR ", $whereOr);
}

$orderby = array(
  "product_delivery.service_id", 
  "DATE_FORMAT(product_delivery_trace.date_delivery, '%Y-%m')",
  //"product_delivery.stock_id", 
);

$groupby = array(
  "product_delivery.service_id",
  "DATE_FORMAT(product_delivery_trace.date_delivery, '%Y-%m')",
  //"product_delivery.stock_id",
  // "type",
);

$ljoin = array(
  "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id",
);

$trace = new CProductDeliveryTrace;

$req = new CRequest;
$req->addTable("product_delivery_trace");
$req->addSelect($select);
$req->addWhere($where);
$req->addGroup($groupby);
$req->addLJoin($ljoin);
$req->addOrder($ljoin);
$totals = $trace->_spec->ds->loadList($req->getRequest());

$table = array();
foreach($totals as $_values) {
  $_service_id = $_values["service_id"] ? $_values["service_id"] : "none";
  if (!isset($table[$_service_id]))
    $table[$_service_id] = array();
  
  $date = $_values["date"]."-01";
  if (!isset($table[$_service_id][$date])) {
    $table[$_service_id][$date] = 0;
  }
  
  $table[$_service_id][$date] += $_values["total"];
}

$services = CProductStockGroup::getServicesList();
$services["none"] = new CService;
$services["none"]->_view = CAppUI::tr("None");

$dates = array();
while ($_date_min <= $_date_max) {
  $dates[] = mbTransformTime(null, $_date_min, "%Y-%m");
  $_date_min = mbDate("+1 MONTH", $_date_min);
}

foreach($services as $_key => $_service) {
  if (!isset($table[$_key])) {
    $table[$_key] = array();
  }
  
  foreach($dates as $_date) {
    $_date = $_date."-01";
    if (!isset($table[$_key][$_date])) {
      $table[$_key][$_date] = null;
    }
  }
  
  ksort($table[$_key]);
}

$GLOBALS["services"] = $services;

function service_sort($v1, $v2) {
  $keys = array_keys($GLOBALS["services"]);
  return array_search($v1, $keys) - array_search($v2, $keys);
}

uksort($table, "service_sort");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivery', $delivery);
$smarty->assign('services', $services);
$smarty->assign('table', $table);
$smarty->assign('dates', $dates);

$smarty->display('inc_vw_loss.tpl');
