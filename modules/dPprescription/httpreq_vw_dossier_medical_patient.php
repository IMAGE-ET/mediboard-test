<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$patient_id = mbGetValueFromGet("patient_id");
$sejour_id = mbGetValueFromGet("sejour_id");
$prescription_sejour_id = mbGetValueFromGet("prescription_sejour_id");
$praticien_id = mbGetValueFromGet("praticien_id");

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

$user_id = $praticien_id ? $praticien_id : $AppUI->user_id;

$user = new CMediusers();
$user->load($user_id);
$user->isPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("user", $user);
$smarty->assign("prescription_sejour_id", $prescription_sejour_id);
$smarty->display("inc_dossier_medical_patient.tpl");

?>