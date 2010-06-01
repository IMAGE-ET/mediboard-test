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
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("category", $category);
$smarty->assign("element_prescription_id", $element_prescription_id);
$smarty->display("inc_list_elements.tpl");

?>