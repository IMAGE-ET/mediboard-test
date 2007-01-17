<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération des services
$order = "group_id, nom";
$where = array();
$where["group_id"] = db_prepare_in(array_keys($etablissements));
$services = new CService;
$services = $services->loadList($where, $order);

$aListServices = array();
foreach($services as $keyService=>$service){
  $aListServices[$service->group_id][$service->_id] = $service;
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("etablissements"  , $etablissements);
$smarty->assign("aListServices"   , $aListServices);

$smarty->display("httpreq_get_services_offline.tpl");
?>