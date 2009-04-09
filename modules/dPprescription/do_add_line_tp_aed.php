<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/


global $AppUI;

$code_cip = mbGetValueFromPost("code_cip");
if(!$code_cip){
  CApp::rip();
}

$token_poso = mbGetValueFromPost("token_poso");
$_patient_id = mbGetValueFromPost("_patient_id");
$debut = mbGetValueFromPost("debut");
$fin = mbGetValueFromPost("fin");
$commentaire = mbGetValueFromPost("commentaire");
$praticien_id = mbGetValueFromPost("praticien_id");

// Recuperation du dossier medical du patient et creation s'il n'existe pas
$dossier_medical_id = CDossierMedical::dossierMedicalId($_patient_id,"CPatient");
$dossier_medical = new CDossierMedical();
$dossier_medical->load($dossier_medical_id);
$dossier_medical->loadRefPrescription();

// Si aucune prescription liée au dossier medical, creation de la prescription
if(!$dossier_medical->_ref_prescription->_id){
  $prescription = new CPrescription();
  $prescription->object_id = $dossier_medical->_id;
  $prescription->object_class = $dossier_medical->_class_name;
  $prescription->type = "traitement";
  $msg = $prescription->store();
  $prescription_id = $prescription->_id;
} else {
  $prescription_id = $dossier_medical->_ref_prescription->_id;
}

$line = new CPrescriptionLineMedicament();
$line->prescription_id = $prescription_id;
$line->code_cip = $code_cip;
$line->creator_id = $AppUI->user_id;
$line->praticien_id = $praticien_id;
$line->debut = $debut;
$line->fin = $fin;
$line->commentaire = $commentaire;
$line->emplacement = "service";
$msg = $line->store();

$posos = explode("|", $token_poso);
foreach($posos as $poso){
  $explode_poso = explode("_", $poso);
  $quantite    = @$explode_poso[0];
  $unite_prise = @$explode_poso[1];
  $moment      = @$explode_poso[2];
  
  if($moment){
	  $moment_unitaire = new CMomentUnitaire();
	  $moment_unitaire->libelle = "le $moment";
	  $moment_unitaire->loadMatchingObject();
  }
	$prise_poso = new CPrisePosologie();
	$prise_poso->object_id = $line->_id;
	$prise_poso->object_class = $line->_class_name;
	$prise_poso->unite_prise = $unite_prise;
	$prise_poso->quantite = $quantite;
	if($moment && $moment_unitaire->_id){
	  $prise_poso->moment_unitaire_id = $moment_unitaire->_id;
	}
	$msg = $prise_poso->store();
}

 CApp::rip();

?>