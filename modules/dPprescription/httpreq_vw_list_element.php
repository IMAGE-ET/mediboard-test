<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$category_prescription_id = CValue::getOrSession("category_prescription_id");
$element_prescription_id = CValue::getOrSession("element_prescription_id");

$associations = array();
$executants = array();

$category = new CCategoryPrescription();
$category->load($category_prescription_id);

if($element_prescription_id && !$category_prescription_id){
	$element_prescription = new CElementPrescription();
	$element_prescription->load($element_prescription_id);
	$element_prescription->loadRefCategory();
	$category =& $element_prescription->_ref_category_prescription;
}

if($category_prescription_id){
	$category->loadElementsPrescription();
	foreach ($category->_ref_elements_prescription as $_element) {
	  $_element->countBackRefs("cdarrs");
	}
	
	// Chargement de la liste des functions associés à la catégorie selectionee
	$function_cat_prescription = new CFunctionCategoryPrescription();
	$function_cat_prescription->category_prescription_id = $category->_id;
	$associations = $function_cat_prescription->loadMatchingList();
	
	foreach($associations as &$_association){
	  $_association->loadRefFunction();
	}
	
	// Chargement des autres executants
	$executant_prescription_line = new CExecutantPrescriptionLine();
  $executant_prescription_line->category_prescription_id = $category->_id;
	$executants = $executant_prescription_line->loadMatchingList(); 
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("category", $category);
$smarty->assign("element_prescription_id", $element_prescription_id);
$smarty->assign("associations", $associations);
$smarty->assign("executants", $executants);
$smarty->display("inc_list_elements.tpl");

?>