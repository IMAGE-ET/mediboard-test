<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$autoadd_default = CAppUI::pref("AUTOADDSIGN", true);

$sejour_id   = CValue::post("_sejour_id");
$del         = CValue::post("del");
$patient_id  = CValue::post("_patient_id");

// Sejour
// si on a un sejour et que l'option d'ajout automatique est activée
if ($sejour_id && $autoadd_default){
  $doSejour = new CDoObjectAddEdit("CTraitement");
 
  // Ajout du traitement dans le sejour
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($sejour_id,"CSejour");
  $doSejour->redirectStore     = null;
  $doSejour->redirect          = null;
 
  $doSejour->doIt();
}

// Patient
$doPatient = new CDoObjectAddEdit("CTraitement");
if ($del != 1){
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($patient_id,"CPatient");
}

$_POST["ajax"] = 1;
  
$doPatient->doIt();

?>