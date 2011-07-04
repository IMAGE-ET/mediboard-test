<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$num_days_date_min = CAppUI::conf("pharmacie num_days_date_min");
$datetime_min = CValue::getOrSession('_datetime_min', mbDate("-$num_days_date_min DAY")." 00:00:00");
$datetime_max = CValue::getOrSession('_datetime_max', mbDate("+2 DAY")." 23:59:59");

$order_col    = CValue::getOrSession('order_col', 'date_dispensation');
$order_way    = CValue::getOrSession('order_way', 'DESC');

$delivrance = new CProductDelivery();
$delivrance->_datetime_min = $datetime_min;
$delivrance->_datetime_max = $datetime_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('order_col',  $order_col);
$smarty->assign('order_way',  $order_way);
$smarty->assign('delivrance', $delivrance);

$smarty->display('vw_idx_delivrance.tpl');
