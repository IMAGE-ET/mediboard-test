<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can, $m, $g;

$can->needsAdmin();

// Infos sur l'id externe
$tag = "sherpa group:$g";
$today = mbDateTime();

// Chargement des praticiens de l'établissement
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();
foreach($praticiens as &$curr_prat) {
  $curr_prat->loadLastId400($tag);
}

// Chargement de services
$salle = new CSalle();
$salle->group_id = $g;
$order = "nom";
$salles = $salle->loadMatchingList($order);

foreach ($salles as &$_salle) {
	$_salle->loadLastId400($tag);
}


// Chargement de services
$service = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $service->loadList($where, $order);

foreach($services as &$curr_service) {
  $curr_service->loadRefs();
  foreach($curr_service->_ref_chambres as &$curr_chambre) {
    $curr_chambre->loadRefs();
    foreach($curr_chambre->_ref_lits as &$curr_lit) {
      $curr_lit->loadLastId400($tag);
    }
  }
}

// Chargement des etablissements externes
$orderEtab = "nom";
$etabExterne = new CEtabExterne();
$listEtabExternes = $etabExterne->loadList(null, $orderEtab);
foreach($listEtabExternes as &$etabExterne){
	$etabExterne->loadLastId400($tag);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tag"       , $tag);
$smarty->assign("today"     , $today);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("services"  , $services);
$smarty->assign("salles"    , $salles);
$smarty->assign("listEtabExternes", $listEtabExternes);

$smarty->display("view_idsherpa.tpl");
?>