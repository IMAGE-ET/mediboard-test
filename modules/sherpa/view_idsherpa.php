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
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();
foreach ($praticiens as &$curr_prat) {
  $curr_prat->loadLastId400($tag);
}

// Chargement des praticiens de l'établissement
$personnelsAidesOp = CPersonnel::loadListPers("op");
$personnelsPanseuses = CPersonnel::loadListPers("op_panseuse");
$persusers = array();
$persusersAidesOp = array();
$persusersPanseuses = array();
$persusersType = array();

foreach($personnelsAidesOp as &$curr_pers){
  $curr_pers->loadRefUser();
  $curr_pers->_ref_user->loadLastId400($tag);
  $persusersAidesOp["user-".$curr_pers->_ref_user->_id] = $curr_pers->_ref_user;
  $persusersType[$curr_pers->_ref_user->_id]["op"] = 1;
}

foreach($personnelsPanseuses as &$curr_pers){
  $curr_pers->loadRefUser();
  $curr_pers->_ref_user->loadLastId400($tag);
  $persusersPanseuses["user-".$curr_pers->_ref_user->_id] = $curr_pers->_ref_user;
  $persusersType[$curr_pers->_ref_user->_id]["op_panseuse"] = 1;
}

$persusers = array_merge($persusersAidesOp, $persusersPanseuses);


// Chargement de services
$salle = new CSalle();
$salles = $salle->loadGroupList();

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

// Chargement des niveaux de prestations
$prestations = CPrestation::loadCurrentList();
foreach($prestations as &$prestation) {
  $prestation->loadLastId400($tag);
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

$smarty->assign("persusersType"    , $persusersType);
$smarty->assign("tag"              , $tag);
$smarty->assign("today"            , $today);
$smarty->assign("praticiens"       , $praticiens);
$smarty->assign("persusers"        , $persusers);
$smarty->assign("services"         , $services);
$smarty->assign("prestations"      , $prestations);
$smarty->assign("salles"           , $salles);
$smarty->assign("listEtabExternes" , $listEtabExternes);

$smarty->display("view_idsherpa.tpl");
?>