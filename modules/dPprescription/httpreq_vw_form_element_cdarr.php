<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$element_prescription_id = CValue::getOrSession("element_prescription_id");
$element_prescription_to_cdarr_id = CValue::getOrSession("element_prescription_to_cdarr_id");

$element_prescription_to_cdarr = new CElementPrescriptionToCdarr();
$element_prescription_to_cdarr->load($element_prescription_to_cdarr_id);

$element_prescription_to_cdarr->loadRefElementPrescription();
$element_prescription =& $element_prescription_to_cdarr->_ref_element_prescription;

if(!$element_prescription->_id && $element_prescription_id){
	$element_prescription->load($element_prescription_id);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("element_prescription_to_cdarr", $element_prescription_to_cdarr);
$smarty->display("inc_form_element_cdarr.tpl");

?>