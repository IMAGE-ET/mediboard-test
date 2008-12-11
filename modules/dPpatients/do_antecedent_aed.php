<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$autoadd_default = isset($AppUI->user_prefs["AUTOADDSIGN"]) ? $AppUI->user_prefs["AUTOADDSIGN"] : 1 ;

// Sejour
// si on a un sejour et que l'option d'ajout automatique est activée
if(isset($_POST["_sejour_id"]) && ($autoadd_default == 1) && ($_POST["_sejour_id"] != "")){
  $doSejour = new CDoObjectAddEdit("CAntecedent", "antecedent_id");
 
  // Ajout de l'antecedent dans le sejour
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($_POST["_sejour_id"],"CSejour");
  $doSejour->redirectStore = null;
  $doSejour->redirect = null;
 
  $doSejour->doIt();
}

// Patient
$doPatient = new CDoObjectAddEdit("CAntecedent", "antecedent_id");

if($_POST["del"] != 1 && isset($_POST["_patient_id"])){
  $_POST["dossier_medical_id"] = CDossierMedical::dossierMedicalId($_POST["_patient_id"],"CPatient");
}
$_POST["ajax"] = 1;
  
$doPatient->doIt();

?>