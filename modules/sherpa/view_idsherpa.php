<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can, $m, $g;

$can->needsAdmin();

// Last update

$today = mbDateTime();

// Chargement des praticiens de l'établissement

$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();
foreach($praticiens as &$curr_prat) {
  $curr_prat->loadLastId400("sherpa group:$g");
}

// Chargement de services

$service = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $service->loadList($where, $order);

foreach($services as &$curr_service) {
  $curr_service->loadRefs();
  $curr_service->loadLastId400("sherpa group:$g");
  foreach($curr_service->_ref_chambres as &$curr_chambre) {
    $curr_chambre->loadRefs();
    foreach($curr_chambre->_ref_lits as &$curr_lit) {
      $curr_lit->loadLastId400("sherpa group:$g");
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"     , $today);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("services"  , $services);

$smarty->display("view_idsherpa.tpl");
?>