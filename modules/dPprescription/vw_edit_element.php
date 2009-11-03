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
$element_prescription_id = CValue::getOrSession("element_prescription_id");
$mode_duplication = CValue::get("mode_duplication", "0");
$elements_prescription = array();
$element_prescription = new CElementPrescription();

// Chargement des elements de prescription de la categorie selectionnee
$order = "libelle";
$element_prescription->category_prescription_id = $category_id;
$elements_prescription = $element_prescription->loadMatchingList($order);	

// Chargement de la liste des categories
$category = new CCategoryPrescription();
$categories = $category->loadCategoriesByChap(null, "current");

// Chargement de la categorie selectionnee
$category->load($category_id);

// Chargement de l'element selectionne
$element_prescription->load($element_prescription_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mode_duplication", $mode_duplication);
$smarty->assign("category", $category);
$smarty->assign("category_id", $category_id);
$smarty->assign("categories"   , $categories);
$smarty->assign("element_prescription"     , $element_prescription);
$smarty->assign("elements_prescription", $elements_prescription);
$smarty->display("vw_edit_element.tpl");

?>