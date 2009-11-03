<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$category_id = CValue::getOrSession("category_id");

// Chargement de la catégorie demandé
$category=new CCategory;
$category->load($category_id);
$category->loadRefsBack();

// Liste des Catégories
$lstCategory = new CCategory;
$listCategory = $lstCategory->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCategory", $listCategory);
$smarty->assign("category"    , $category    );

$smarty->display("vw_idx_category.tpl");

?>