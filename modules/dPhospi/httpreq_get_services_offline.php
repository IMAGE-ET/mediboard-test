<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkRead();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération des services
$order = "group_id, nom";
$where = array();
$where["group_id"]  = CSQLDataSource::prepareIn(array_keys($etablissements));
$where["cancelled"] = "= '0'";
$services = new CService();
$services = $services->loadList($where, $order);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("services"       , $services);

$smarty->display("httpreq_get_services_offline.tpl");
?>