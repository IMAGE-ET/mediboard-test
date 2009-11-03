<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = CValue::get("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);

$patient_id = $prescription->_ref_object->patient_id;

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;

if($dossier_medical->_id){
  $dossier_medical->loadRefPrescription();
  if($dossier_medical->_ref_prescription->_id){
    $dossier_medical->_ref_prescription->loadRefsLinesMed();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->display("inc_dossier_medical_patient.tpl");

?>