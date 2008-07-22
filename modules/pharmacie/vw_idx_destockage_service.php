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


// remplissage de liste destockages (a titre d'exemple de structure

$list_destockages = array(
  '0645821' => array( /// Code CIP
    'quantite' => 3,
    'conditionnement' => 'Boites',
  ), 
  
  /// ...

);




// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_destockages', $list_destockages);
$smarty->assign('service_id',       $service_id);
$smarty->assign('list_services',    $list_services);
$smarty->assign('delivrance',       $delivrance);

$smarty->display('vw_idx_destockage_service.tpl');

?>