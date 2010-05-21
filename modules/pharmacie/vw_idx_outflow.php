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

$where = array(
  "date_delivery" => "BETWEEN '$date_min' AND '$date_max'",
  "service_id" => "IS NULL",
);

$list_outflows = $delivrance->loadList($where, "date_delivery");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivrance',    $delivrance);
$smarty->assign('list_outflows', $list_outflows);

$smarty->display('vw_idx_outflow.tpl');

?>