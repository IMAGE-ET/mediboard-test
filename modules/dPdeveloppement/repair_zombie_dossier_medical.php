<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision: $
 * @author Romain Ollivier
 */

global $AppUI;

$patient_id         = mbGetValueFromGet("patient_id");
$dossier_medical_id = mbGetValueFromGet("dossier_medical_id");

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefDossierMedical();
$dossierZombie = new CDossierMedical();
$dossierZombie->load($dossier_medical_id);
if($patient->_ref_dossier_medical->_id) {
  $mergedDossier = new CDossierMedical();
  $mergedDossier->mergeDBFields(array($dossierZombie, $patient->_ref_dossier_medical));
  $mergedDossier->object_class = "CPatient";
  $mergedDossier->object_id    = $patient->_id;
  $result = $mergedDossier->merge(array($dossierZombie, $patient->_ref_dossier_medical));
  if($result) {
    $AppUI->setMsg($result, UI_MSG_ERROR);
  } else {
    $AppUI->setMsg("Fusion du zombie", UI_MSG_OK);
  }
} else {
  $dossierZombie->object_id = $patient->_id;
  $result = $dossierZombie->store();
  if($result) {
    $AppUI->setMsg($result, UI_MSG_ERROR);
  } else {
    $AppUI->setMsg("Rיparation du lien", UI_MSG_OK);
  }
}

echo $AppUI->getMsg();

?>