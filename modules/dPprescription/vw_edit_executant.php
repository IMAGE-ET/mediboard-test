<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$category_id = CValue::getOrSession("category_id");
$executant_prescription_line_id = CValue::get("executant_prescription_line_id");

$executants = array();
$functions_cat = array();
$executant_prescription_line = new CExecutantPrescriptionLine();

// Chargement des executants de la categorie selectionnee
$executant_prescription_line->category_prescription_id = $category_id;
$executants = $executant_prescription_line->loadMatchingList();	

// Chargement de la liste des categories
$category = new CCategoryPrescription();
$categories = $category->loadCategoriesByChap(null, "current");

// Chargement de la categorie selectionnee
$category->load($category_id);

// Chargement de l'executant selectionne
$executant_prescription_line->load($executant_prescription_line_id);


// Chargement de la liste des fonctions
$function = new CFunctions();
$functions = $function->loadListWithPerms(PERM_READ);

// Chargement de la liste des functions associés à la catégorie selectionee
$function_cat_prescription = new CFunctionCategoryPrescription();
$function_cat_prescription->category_prescription_id = $category_id;
$associations = $function_cat_prescription->loadMatchingList();

foreach($associations as &$_association){
  $_association->loadRefFunction();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("category"    , $category);
$smarty->assign("category_id" , $category_id);
$smarty->assign("categories"  , $categories);
$smarty->assign("executant_prescription_line"   , $executant_prescription_line);
$smarty->assign("executants"  , $executants);
$smarty->assign("functions", $functions);
$smarty->assign("associations", $associations);
$smarty->display("vw_edit_executant.tpl");

?>