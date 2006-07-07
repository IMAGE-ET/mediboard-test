<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}
 
require_once( $AppUI->getModuleClass("dPmateriel", "category"));

$category_id = mbGetValueFromGetOrSession("category_id", null);

// Chargement de la catégorie demandé
$category=new CCategory;
$category->load($category_id);
$category->loadRefsBack();

// Liste des Catégories
$lstCategory = new CCategory;
$listCategory = $lstCategory->loadList();

// Création du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP;
$smarty->assign("listCategory", $listCategory);
$smarty->assign("category", $category);
$smarty->display('vw_idx_category.tpl');

?>