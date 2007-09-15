<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Récupération de la chambre à ajouter/editer
$chambreSel = new CChambre;
$chambreSel->load(mbGetValueFromGetOrSession("chambre_id", 0));
$chambreSel->loadRefs();

if(!$chambreSel->_id) {
  mbSetValueToSession("lit_id", 0);
}

// Récupération du lit à ajouter/editer
$litSel = new CLit;
$litSel->load(mbGetValueFromGetOrSession("lit_id", 0));
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
$smarty = new CSmartyDP();

$smarty->assign("chambreSel", $chambreSel);
$smarty->assign("litSel"    , $litSel);
$smarty->assign("services"  , $services);

$smarty->display("vw_idx_chambres.tpl");

?>