<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$catalogue_labo_id = mbGetValueFromGetOrSession("catalogue_labo_id");

// Chargement du catalogue demandé
$catalogue = new CCatalogueLabo;
$catalogue->load($catalogue_labo_id);
$catalogue->loadRefs();

// Chargement de tous les catalogues
$where = array("pere_id" => "IS NULL");
$order = "identifiant";
$listCatalogues = $catalogue->loadList($where, $order);
foreach($listCatalogues as &$_catalogue) {
  $_catalogue->loadRefsDeep();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCatalogues", $listCatalogues);
$smarty->assign("catalogue"     , $catalogue    );

$smarty->display("vw_edit_catalogues.tpl");
?>
