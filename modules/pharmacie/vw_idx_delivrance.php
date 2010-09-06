<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$delivrance = new CProductDelivery();

$num_days_date_min = CAppUI::conf("pharmacie num_days_date_min");

$date_min = CValue::get('_date_min');
$date_max = CValue::get('_date_max');

if (!$date_min) {
  $date_min = CValue::session('_date_delivrance_min', mbDate("-$num_days_date_min DAY"));
}
if (!$date_max) {
  $date_max = CValue::session('_date_delivrance_max', mbDate("+2 DAY"));
}

$order_col = CValue::getOrSession('order_col', 'date_dispensation');
$order_way = CValue::getOrSession('order_way', 'DESC');

$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('order_col',  $order_col);
$smarty->assign('order_way',  $order_way);
$smarty->assign('delivrance', $delivrance);

$smarty->display('vw_idx_delivrance.tpl');

?>