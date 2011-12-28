<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkAdmin();

$service_id    = CValue::getOrSession("service_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$uf_id         = CValue::getOrSession("uf_id");
$prestation_id = CValue::getOrSession("prestation_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Chargement du service à ajouter/editer
$service = new CService();
$service->group_id = $group->_id;
$service->load($service_id);
$service->loadRefsNotes();

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
$services = $service->loadListWithPerms(PERM_READ,$where, $order);
foreach ($services as $_service) {
  foreach ($_service->loadRefsChambres() as $_chambre) {
	  $_chambre->loadRefs();
	}
}

// Chargement de l'uf à ajouter/éditer
$uf = new CUniteFonctionnelle();
$uf->group_id = $group->_id;
$uf->load($uf_id);
$uf->loadRefsNotes();

// Récupération des ufs
$order = "group_id, code";
$ufs = $uf->loadList(null, $order);

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

$praticiens = CAppUI::$user->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services"      , $services);
$smarty->assign("service"       , $service);
$smarty->assign("chambre"       , $chambre);
$smarty->assign("lit"           , $lit);
$smarty->assign("ufs"           , $ufs);
$smarty->assign("uf"            , $uf);
$smarty->assign("prestations"   , $prestations);
$smarty->assign("prestation"    , $prestation);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_idx_infrastructure.tpl");
?>