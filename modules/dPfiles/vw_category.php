<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsAdmin();

$file_category_id = mbGetValueFromGetOrSession("file_category_id");

// Chargement de la catégorie demandé
$category=new CFilesCategory;
$category->load($file_category_id);

// Liste des Catégories
$listCategory = new CFilesCategory;
$listCategory = $listCategory->loadList();

// LIste des Class
CAppUI::getAllClasses();
$listClass = getChildClasses();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCategory", $listCategory);
$smarty->assign("category"    , $category    );
$smarty->assign("listClass"   , $listClass   );

$smarty->display("vw_category.tpl");

?>