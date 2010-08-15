<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkRead();

$prestation_id = CValue::getOrSession("prestation_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$service_id    = CValue::getOrSession("service_id");

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Récupération de la chambre à ajouter/editer
$chambre = new CChambre();
$chambre->load($chambre_id);
$chambre->loadRefsLits();
$chambre->loadRefService();

if (!$chambre->_id) {
  CValue::setSession("lit_id", 0);
}

// Chargement du lit à ajouter/editer
$lit = new CLit();
$lit->load($lit_id);
$lit->loadRefChambre();

// Chargement du service à ajouter/editer
$service = new CService();
$service->load($service_id);

// Récupération des chambres/services
$group = CGroups::loadCurrent();
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ,$where, $order);
foreach ($services as $_service) {
  foreach ($_service->loadRefsChambres() as $_chambre) {
	  $_chambre->loadRefs();
	}
}

// Chargement de la prestation
$prestation = new CPrestation();
$prestation->load($prestation_id);

// Récupération des prestations
$order = "group_id, nom";
$prestations = $prestation->loadList(null, $order);
foreach ($prestations as $_prestation){
  $_prestation->loadRefGroup();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("chambre"  , $chambre);
$smarty->assign("lit"      , $lit);
$smarty->assign("service"  , $service);
$smarty->assign("services" , $services);
$smarty->assign("prestation" , $prestation);
$smarty->assign("prestations", $prestations);

$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_idx_chambres.tpl");
?>