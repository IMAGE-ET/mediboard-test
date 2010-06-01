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

$delivrance = new CProductDelivery();
$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;
$delivrance->quantity = 1;
$delivrance->date_delivery = mbDateTime();

$date_max = mbDate("+1 DAY", $date_max);
$where = array(
  "date_delivery" => "BETWEEN '$date_min' AND '$date_max'",
  "manual = '1'",
);

$list_outflows = $delivrance->loadList($where, "service_id, date_delivery");

$service = new CService;
$list_services = $service->loadListWithPerms(PERM_READ);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivrance',    $delivrance);
$smarty->assign('list_outflows', $list_outflows);
$smarty->assign('list_services', $list_services);

$smarty->display('vw_idx_outflow.tpl');

?>