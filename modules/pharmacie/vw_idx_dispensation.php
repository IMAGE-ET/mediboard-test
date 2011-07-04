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

$service_id = CValue::getOrSession('service_id');
$patient_id = CValue::getOrSession('patient_id');

$delivrance = new CProductDelivery();
$delivrance->_datetime_min = $datetime_min;
$delivrance->_datetime_max = $datetime_max;

// Services list
$list_services = CProductStockGroup::getServicesList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('patient_id', $patient_id);
$smarty->assign('service_id', $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('delivrance', $delivrance);

$smarty->display('vw_idx_dispensation.tpl');

?>