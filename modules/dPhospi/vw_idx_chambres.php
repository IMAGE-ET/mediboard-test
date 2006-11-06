<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Récupération de la chambre à ajouter/editer
$chambreSel = new CChambre;
$chambreSel->load(mbGetValueFromGetOrSession("chambre_id"));
$chambreSel->loadRefs();

// Récupération du lit à ajouter/editer
$litSel = new CLit;
$litSel->load(mbGetValueFromGetOrSession("lit_id"));
$litSel->loadRefs();

// Récupération des chambres/services
$services = new CService;
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

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("chambreSel", $chambreSel);
$smarty->assign("litSel"    , $litSel);
$smarty->assign("services"  , $services);

$smarty->display("vw_idx_chambres.tpl");

?>