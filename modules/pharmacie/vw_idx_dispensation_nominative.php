<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Alexis Granger
 */

global $can, $g;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');
$patient_id = mbGetValueFromGetOrSession('patient_id');

// Services list
$service = new CService();
$service->group_id = $g;
$services = $service->loadMatchingList('nom');

$date = mbDate();
$delivrance = new CProductDelivery();
$delivrance->_date_min = $date.' 00:00:00';
$delivrance->_date_max = $date.' 23:59:59';

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient_id", $patient_id);
$smarty->assign('service_id',    $service_id);
$smarty->assign('services', $services);
$smarty->assign('delivrance', $delivrance);

$smarty->display('vw_idx_dispensation_nominative.tpl');

?>