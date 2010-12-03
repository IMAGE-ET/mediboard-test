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
$_date_min = CValue::get("_date_min");
$_date_max = CValue::get("_date_max");

CValue::setSession("type", $type);
CValue::setSession("_date_min", $_date_min);
CValue::setSession("_date_max", $_date_max);

//CMbObject::$useObjectCache = false;
set_time_limit(300);
$limit = 1000;

$delivery = new CProductDelivery; // CProductDeliveryTrace has dateTime specs (not CProductDelivery)
$delivery->type = $type;
$delivery->_date_min = $_date_min;
$delivery->_date_max = $_date_max;

$where = array(
  "product_delivery_trace.date_delivery" => "BETWEEN '$_date_min' AND '$_date_max'",
  "product_delivery.type" => "='$type'",
);

$groupby = array(
  "product_delivery.stock_id",
  "product_delivery.service_id",
);

$ljoin = array(
  "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_id",
);

$trace = new CProductDeliveryTrace;
$traces = $trace->loadList($where, "product_delivery_trace.date_delivery", null, $groupby, $ljoin);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivery', $delivery);
$smarty->assign('traces', $traces);

$smarty->display('inc_vw_loss.tpl');
