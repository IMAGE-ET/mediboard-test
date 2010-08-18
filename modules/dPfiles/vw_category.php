<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsAdmin();

$file_category_id = CValue::getOrSession("file_category_id");

// Chargement de la catégorie demandé
$category = new CFilesCategory;
$category->load($file_category_id);
$category->countDocItems();

// Liste des Catégories
$categories = $category->loadList(null, "class, nom");

// Liste des Classes disponibles
$listClass = CApp::getChildClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories"  , $categories);
$smarty->assign("category"    , $category    );
$smarty->assign("listClass"   , $listClass   );

$smarty->display("vw_category.tpl");

?>