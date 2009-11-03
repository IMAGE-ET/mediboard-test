<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$prestation_id = CValue::getOrSession("prestation_id", 0);
$chambre_id    = CValue::getOrSession("chambre_id", 0);
$lit_id        = CValue::getOrSession("lit_id", 0);
$service_id    = CValue::getOrSession("service_id", 0);

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération de la chambre à ajouter/editer
$chambreSel = new CChambre();
$chambreSel->load($chambre_id);
$chambreSel->loadRefs();

if(!$chambreSel->_id) {
  CValue::setSession("lit_id", 0);
}

// Chargement du lit à ajouter/editer
$litSel = new CLit();
$litSel->load($lit_id);
$litSel->loadRefs();

// Chargement du service à ajouter/editer
$serviceSel = new CService();
$serviceSel->load($service_id);

// Chargement de la prestation
$prestation = new CPrestation();
$prestation->load($prestation_id);

// Récupération des chambres/services
$services = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);
foreach ($services as $service_id => $service) {
  $services[$service_id]->loadRefs();
  $chambres =& $services[$service_id]->_ref_chambres;
  foreach ($chambres as $chambre_id => $chambre) {
	  $chambres[$chambre_id]->loadRefs();
	}
}

// Récupération des prestations
$order = "group_id, nom";
$prestations = new CPrestation();
$prestations = $prestations->loadList(null, $order);
foreach($prestations as $keyPrestation=>$valPrestation){
  $prestations[$keyPrestation]->loadRefGroup();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("chambreSel"  , $chambreSel);
$smarty->assign("litSel"      , $litSel);

$smarty->assign("serviceSel"  , $serviceSel);
$smarty->assign("services"    , $services);

$smarty->assign("prestation"  , $prestation);
$smarty->assign("prestations" , $prestations);

$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_idx_chambres.tpl");

?>