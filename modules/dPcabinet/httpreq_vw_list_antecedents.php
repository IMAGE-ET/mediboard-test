<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

$canPatient = CModule::getCanDo("dPpatients");
$canPatient->needsEdit();

$patient_id  = CValue::getOrSession("patient_id", 0);
$_is_anesth  = CValue::getOrSession("_is_anesth", null);
$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPrescriptionSejour();

$patient = new CPatient;
$patient->load($patient_id);

// Chargement du dossier medical du patient
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;

// Chargements des antecedents et traitements du dossier_medical
if ($dossier_medical->_id) {
	$dossier_medical->loadRefsAntecedents(true); // On doit charger TOUS les antecedents, meme les annulés (argument true)
	$dossier_medical->loadRefsTraitements();
	$dossier_medical->countAntecedents();
  $dossier_medical->countTraitements();

	$prescription = $dossier_medical->loadRefPrescription();
	
  foreach ($dossier_medical->_all_antecedents as $_antecedent) {
    $_antecedent->loadLogs();
  }
  
  if ($prescription && is_array($prescription->_ref_prescription_lines)) {
    foreach($prescription->_ref_prescription_lines as $_line) {
      $_line->loadRefsPrises();
    }
  }
}

$user = CAppUI::$user;
$user->isPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour" , $sejour);
$smarty->assign("patient"    , $patient);
$smarty->assign("_is_anesth" , $_is_anesth);
$smarty->assign("user", $user);


$smarty->display("inc_list_ant.tpl");

?>