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
$list_outflows = $delivrance->loadList($where, "date_delivery DESC, service_id", "$start, 30");
$total_outflows = $delivrance->countList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('start', $start);
$smarty->assign('list_outflows', $list_outflows);
$smarty->assign('total_outflows', $total_outflows);

$smarty->display('inc_outflows_list.tpl');
