<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$patients_id = CValue::get('patients_id');

if(count($patients_id) < 2) {
  CAppUI::setMsg("Veuillez selectionner deux patients", UI_MSG_ALERT);
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
    $finalPatient->patient_id = null;
  }
  
  $patient->loadRefsFwd();
  $patient->updateNomPaysInsee();
  $patients[] = $patient;
}
  
$checkMerge = $patient->checkMerge($patients);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient1"    , $patients[0]);
$smarty->assign("patient2"    , $patients[1]);
$smarty->assign("finalPatient", $finalPatient);
$smarty->assign("testMerge"   , $checkMerge);

$smarty->display("fusion_pat.tpl");
?>