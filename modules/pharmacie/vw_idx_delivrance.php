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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('service_id',    $service_id);
$smarty->assign('list_services', $list_services);

$smarty->assign('delivrance', new CProductDelivery());

$smarty->display('vw_idx_delivrance.tpl');

?>