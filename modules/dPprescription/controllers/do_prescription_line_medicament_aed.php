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
/*
if(!$prescription_line_medicament_id && CBcbProduit::isOxygene($code_cip)){
	$prescription_line_mix = new CPrescriptionLineMix();
  $prescription_line_mix->bindOxygene($_POST);
	CApp::rip();
}*/
// do_aed standard
//else {
	$do = new CDoObjectAddEdit("CPrescriptionLineMedicament", "prescription_line_medicament_id");
	$do->doIt();
//}
	


?>