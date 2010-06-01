<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$element_prescription_to_cdarr_id = CValue::getOrSession("element_prescription_to_cdarr_id");
$element_prescription_id = CValue::getOrSession("element_prescription_id");

// Chargement de l'element de prescription
$element_prescription = new CElementPrescription();
$element_prescription->load($element_prescription_id);

// Chargement des actes cdarrs
$element_prescription->loadBackRefs("cdarrs");

$cdarrs = array();
if(count($element_prescription->_back["cdarrs"])){
	foreach($element_prescription->_back["cdarrs"] as $_acte_cdarr){
		$_acte_cdarr->loadRefActiviteCdarr();
		$_activite_cdarr =& $_acte_cdarr->_ref_activite_cdarr;
		$cdarrs[$_activite_cdarr->type][] = $_acte_cdarr;
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("element_prescription_to_cdarr_id", $element_prescription_to_cdarr_id);
$smarty->assign("cdarrs", $cdarrs);
$smarty->display("inc_list_cdarrs.tpl");

?>