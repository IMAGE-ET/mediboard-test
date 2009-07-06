<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsEdit();

$service_id = mbGetValueFromGetOrSession('service_id');
$start = mbGetValueFromGetOrSession('start', 0);

// Services list
$service = new CService();
$list_services = $service->loadGroupList();

$date = mbDate();
$delivrance = new CProductDelivery();
$date_min = mbGetValueFromGetOrSession('_date_min', $date);
$date_max = mbGetValueFromGetOrSession('_date_max', $date);

mbSetValueToSession('_date_min', $date_min);
mbSetValueToSession('_date_max', $date_max);

$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('service_id',    $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('delivrance',    $delivrance);
$smarty->assign('start',         $start);

$smarty->display('vw_stocks_service.tpl');
