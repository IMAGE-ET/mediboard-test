<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$code_cip = CValue::post("code_cip");
$prescription_line_medicament_id = CValue::post("prescription_line_medicament_id");

// Creation d'une ligne d'oxygene
if(!$prescription_line_medicament_id && CBcbProduit::isOxygene($code_cip)){
	$prescription_line_mix = new CPrescriptionLineMix();
  $prescription_line_mix->bindOxygene($_POST);
	CAppUI::callbackAjax("updateModaleAfterAddOxygene", $prescription_line_mix->_guid);
	CApp::rip();
}
// do_aed standard
else {
	if ($prescription_line_medicament_id && CValue::post("signee") == 1) {
		$guid = "CPrescriptionLineMedicament-$prescription_line_medicament_id";
		$ex_classes = CExClass::getExClassesForObject($guid, "signature", "required");
		echo CExClass::getJStrigger($ex_classes);
	}
	
	$do = new CDoObjectAddEdit("CPrescriptionLineMedicament", "prescription_line_medicament_id");
	$do->doIt();
}

?>