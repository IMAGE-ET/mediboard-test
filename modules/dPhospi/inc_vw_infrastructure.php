<?php /* $Id: inc_vw_infrastructure.php 13247 2011-09-23 08:43:46Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 13247 $
* @author 
*/

CCanDo::checkAdmin();

$service_id    = CValue::get("service_id");
$chambre_id    = CValue::get("chambre_id");
$lit_id        = CValue::get("lit_id");
$uf_id         = CValue::get("uf_id");
$prestation_id = CValue::get("prestation_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

if($service_id != null){
	// Chargement du service à ajouter/editer
	$service = new CService();
	$service->group_id = $group->_id;
	$service->load($service_id);
	$service->loadRefsNotes();
}
if($chambre_id != null){
	// Récupération de la chambre à ajouter/editer
	$chambre = new CChambre();
	$chambre->load($chambre_id);
	$chambre->loadRefsNotes();
	$chambre->loadRefService();
	foreach ($chambre->loadRefsLits() as $_lit) {
	  $_lit->loadRefsNotes();
	}
	
	if (!$chambre->_id) {
	  CValue::setSession("lit_id", 0);
	}

		// Chargement du lit à ajouter/editer
		$lit = new CLit();
		$lit->load($lit_id);
		$lit->loadRefChambre();
	
	// Récupération des chambres/services
	$where = array();
	$where["group_id"] = "= '$group->_id'";
	$order = "nom";
	
  $service = new CService();
	$services = $service->loadListWithPerms(PERM_READ,$where, $order);
	foreach ($services as $_service) {
	  foreach ($_service->loadRefsChambres() as $_chambre) {
	    $_chambre->loadRefs();
	  }
	}
}
if($uf_id != null){
	// Chargement de l'uf à ajouter/éditer
	$uf = new CUniteFonctionnelle();
	$uf->group_id = $group->_id;
	$uf->load($uf_id);
	$uf->loadRefsNotes();
	
	// Récupération des ufs
	$order = "group_id, code";
	$ufs = $uf->loadList(null, $order);
}
if($prestation_id != null){
	// Chargement de la prestation à ajouter/éditer
	$prestation = new CPrestation();
	$prestation->group_id = $group->_id;
	$prestation->load($prestation_id);
	$prestation->loadRefsNotes();
	
	// Récupération des prestations
	$order = "group_id, nom";
	$prestations = $prestation->loadList(null, $order);
	foreach ($prestations as $_prestation){
	  $_prestation->loadRefGroup();
	}
}

$praticiens = CAppUI::$user->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

if($service_id != null){
	$smarty->assign("service"       , $service);
  $smarty->display("inc_vw_service.tpl");
}
elseif($chambre_id != null){
  $smarty->assign("services"       , $services);
	$smarty->assign("chambre"       , $chambre);
	$smarty->assign("lit"           , $lit);
  $smarty->display("inc_vw_chambre.tpl");
}
elseif($uf_id != null){
  $smarty->assign("uf"            , $uf);
  $smarty->display("inc_vw_uf.tpl");
}
elseif($prestation_id != null){
  $smarty->assign("prestation"    , $prestation);
  $smarty->display("inc_vw_prestation.tpl");
}
?>