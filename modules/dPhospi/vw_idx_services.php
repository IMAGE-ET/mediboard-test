<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération du service à ajouter/editer
$serviceSel = new CService;
$serviceSel->load(mbGetValueFromGetOrSession("service_id"));


// Récupération des services
$order = "group_id, nom";
$where = array();
$where["group_id"] = "IN (".implode(array_keys($etablissements),", ").")";
$services = new CService;
$services = $services->loadList($where, $order);
foreach($services as $keyService=>$valService){
  $services[$keyService]->loadRefsFwd();
} 

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("serviceSel"     , $serviceSel    );
$smarty->assign("services"       , $services      );
$smarty->assign("etablissements" , $etablissements);

$smarty->display("vw_idx_services.tpl");

?>