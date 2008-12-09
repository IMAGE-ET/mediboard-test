<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can, $g;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');
$all_stocks = mbGetValueFromGetOrSession('all_stocks');

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
$smarty->assign('all_stocks',    $all_stocks);

$smarty->display('vw_idx_destockage_service.tpl');

?>