<?php 

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
$can->needsRead();

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listEtab", $listEtab);

$smarty->display("httpreq_vw_etab_externes.tpl");

?>