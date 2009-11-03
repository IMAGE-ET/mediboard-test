<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$patient_id = CValue::get("patient_id");

$traitements = array();
$patient = new CPatient();
$patient->load($patient_id);

$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;
if($dossier_medical->_id){
  $dossier_medical->loadRefPrescription();
  if($dossier_medical->_ref_prescription->_id){
    $prescription =& $dossier_medical->_ref_prescription;
		$prescription->loadRefsLinesMed();
    foreach($prescription->_ref_prescription_lines as $_line){
      $traitements[$_line->_id] = $_line->_ref_produit;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("traitements", $traitements);
$smarty->display("inc_vw_select_tp_patient.tpl");

?>