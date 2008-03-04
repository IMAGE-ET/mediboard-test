<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsRead();

$category = new CCategoryPrescription();

// Chargement de la liste des categories
$categories = $category->loadCategoriesByChap();

// Chargement de la category
$category_id = mbGetValueFromGetOrSession("category_id");
$category->load($category_id);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories"   , $categories);
$smarty->assign("category"     , $category);

$smarty->display("vw_edit_category.tpl");

?>