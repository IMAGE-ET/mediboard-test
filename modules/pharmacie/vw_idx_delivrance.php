<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsRead();

$service_id = CValue::getOrSession('service_id');

// Services list
$service = new CService();
$list_services = $service->loadGroupList();

$date = mbDate();
$delivrance = new CProductDelivery();

$date_min = CValue::getOrSession('_date_min', $date);
$date_max = CValue::getOrSession('_date_max', $date);

CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('service_id',    $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('delivrance',    $delivrance);

$smarty->display('vw_idx_delivrance.tpl');

?>