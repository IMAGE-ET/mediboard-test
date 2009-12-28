<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$patients_id = CValue::get('patients_id');

if(count($patients_id) < 2) {
  CAppUI::setMsg("Veuillez sélectionner deux patients", UI_MSG_ALERT);
  CAppUI::redirect("m=dPpatients");
}

$patients = array();
$finalPatient = $checkMerge = null;

// Instance des patients
foreach ($patients_id as $patient_id) {
  $patient = new CPatient;
  
  if (!$patient->load($patient_id)){
    // Erreur sur les ID du patient
    CAppUI::setMsg("Fusion impossible, patient inexistant", UI_MSG_ERROR);
    CAppUI::redirect("m=dPpatients");
  }
  
  if (!$finalPatient) {
    // On base le résultat sur patient1
    $finalPatient = new CPatient;
    $finalPatient->load($patient_id);
    $finalPatient->loadRefsFwd();
    $finalPatient->updateNomPaysInsee();
    $finalPatient->_id = null;
  }
  
  $patient->loadRefsFwd();
  $patient->updateNomPaysInsee();
  $patient->loadIPP();
  $patients[] = $patient;
}
  
$checkMerge = $patient->checkMerge($patients);
$alternative_mode = CModule::getActive("sip") && CAppUI::conf("object_handlers CSipObjectHandler");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient1"    , $patients[0]);
$smarty->assign("patient2"    , $patients[1]);
$smarty->assign("finalPatient", $finalPatient);
$smarty->assign("testMerge"   , $checkMerge);
$smarty->assign("alternative_mode", $alternative_mode);

$smarty->display("fusion_pat.tpl");
?>