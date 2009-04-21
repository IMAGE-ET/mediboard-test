<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");
// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération des services
$order = "group_id, nom";
$where = array();
$where["group_id"] = $ds->prepareIn(array_keys($etablissements));
$services = new CService;
$services = $services->loadList($where, $order);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("services"       , $services);

$smarty->display("httpreq_get_services_offline.tpl");
?>