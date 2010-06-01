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

$category_prescription_id = CValue::get("category_prescription_id");
$element_prescription_id = CValue::get("element_prescription_id");
$element_prescription_to_cdarr_id = CValue::get("element_prescription_to_cdarr_id");

$category_prescription = new CCategoryPrescription();
$element_prescription  = new CElementPrescription();
$element_prescription_to_cdarr = new CElementPrescriptionToCdarr();

if($element_prescription_to_cdarr_id){
	$element_prescription_to_cdarr->load($element_prescription_to_cdarr_id);
	$element_prescription_to_cdarr->loadRefElementPrescription();
	$element_prescription =& $element_prescription_to_cdarr->_ref_element_prescription;
}

if(!$element_prescription->_id && $element_prescription_id){
	$element_prescription->load($element_prescription_id);
}

$element_prescription->loadRefCategory();
$category_prescription =& $element_prescription->_ref_category_prescription;

$cat_id = $category_prescription_id ? $category_prescription_id : $category_prescription->_id;
$category_prescription->load($cat_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("category_prescription_id", $category_prescription->_id);
$smarty->assign("element_prescription_id", $element_prescription->_id);
$smarty->assign("element_prescription_to_cdarr_id", $element_prescription_to_cdarr->_id);
$smarty->display("vw_edit_category.tpl");

?>