<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_min = CValue::get('_date_min', mbDate("-30 DAY"));
$date_max = CValue::get('_date_max', mbDate());
$start = intval(CValue::get('start', 0));

$date_max = mbDate("+1 DAY", $date_max);
$where = array(
  "date_delivery" => "BETWEEN '$date_min' AND '$date_max'",
  "manual = '1'",
);

$delivrance = new CProductDelivery;
$delivrance->quantity = 1;
$delivrance->date_delivery = mbDateTime();

$list_outflows = $delivrance->loadList($where, "date_delivery DESC, service_id", "$start, 30");
$total_outflows = $delivrance->countList($where);

foreach($list_outflows as $_outflow) {
  $_outflow->_ref_stock->_ref_product->getPendingOrderItems(false);
}

$service = new CService;
$list_services = $service->loadListWithPerms(PERM_READ);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('start', $start);
$smarty->assign('delivrance',    $delivrance);
$smarty->assign('list_services', $list_services);
$smarty->assign('list_outflows', $list_outflows);
$smarty->assign('total_outflows', $total_outflows);

$smarty->display('inc_outflows_list.tpl');
