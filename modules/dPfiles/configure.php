<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canEdit) {
	$AppUI->redirect("m=system&a=access_denied");
}

$file_category_id = mbGetValueFromGetOrSession("file_category_id");

// Chargement de la catégorie demandé
$category=new CFilesCategory;
$category->load($file_category_id);

// Liste des Catégories
$listCategory = new CFilesCategory;
$listCategory = $listCategory->loadList();

// LIste des Class
$listClass = getChildClasses();


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("listCategory", $listCategory);
$smarty->assign("category"    , $category    );
$smarty->assign("listClass"   , $listClass   );

$smarty->display("configure.tpl");

?>