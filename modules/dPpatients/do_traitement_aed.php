<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$autoadd_default = CAppUI::pref("AUTOADDSIGN", true);

// Sejour
// si on a un sejour et que l'option d'ajout automatique est activée
if(isset($_POST["_sejour_id"]) && $autoadd_default && ($_POST["_sejour_id"] != "")){
  $doSejour = new CDoObjectAddEdit("CTraitement", "traitement_id");
 
  // Ajout du traitement dans le sejour
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($_POST["_sejour_id"],"CSejour");
  $doSejour->redirectStore = null;
  $doSejour->redirect = null;
 
  $doSejour->doIt();
}

// Patient
$doPatient = new CDoObjectAddEdit("CTraitement", "traitement_id");

if($_POST["del"] != 1){
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($_POST["_patient_id"],"CPatient");
}
$_POST["ajax"] = 1;
  
$doPatient->doIt();

?>