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

// Services list
$service = new CService();
$service->group_id = $g;
$list_services = $service->loadMatchingList('nom');

// Delivrance useful only for the filter
$date = mbDate();
$delivrance = new CProductDelivery();
$delivrance->_date_min = $date;
$delivrance->_date_max = $date.' 23:59:59';

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('service_id',       $service_id);
$smarty->assign('list_services',    $list_services);
$smarty->assign('delivrance',       $delivrance);

$smarty->display('vw_idx_destockage_service.tpl');

?>