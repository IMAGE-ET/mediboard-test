<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$catalogue_labo_id = mbGetValueFromGetOrSession("catalogue_labo_id");
$typeListe  = mbGetValueFromGetOrSession("typeListe");

// Liste des fonctions disponibles
$functions = new CFunctions();
$order = "text";
$functions = $functions->loadListWithPerms(PERM_EDIT, null, $order);

// Chargement du catalogue demandé
$catalogue = new CCatalogueLabo;
$catalogue->load($catalogue_labo_id);
$catalogue->loadRefs();

// Chargement de tous les catalogues
$where = array();
$where["pere_id"] = "IS NULL";
$where[] = "function_id IS NULL OR function_id ".$catalogue->_spec->ds->prepareIn(array_keys($functions));
$where["obsolete"] = " = '0'";
$order = "identifiant";
$listCatalogues = $catalogue->loadList($where, $order);
foreach($listCatalogues as &$_catalogue) {
  $_catalogue->loadRefsDeep();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCatalogues", $listCatalogues);
$smarty->assign("catalogue"     , $catalogue    );

$smarty->display("inc_vw_catalogues.tpl");
?>
