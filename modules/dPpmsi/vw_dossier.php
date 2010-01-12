<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsEdit();

$pat_id = CValue::getOrSession("pat_id");

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);


// Chargement du dossier patient
$patient = new CPatient;
$patient->load($pat_id);

if ($patient->patient_id) {
	$patient->loadRefsFwd();
	$patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical();
  $patient->_ref_dossier_medical->updateFormFields();
  $patient->_ref_dossier_medical->loadRefsAntecedents();
  $patient->_ref_dossier_medical->loadRefsTraitements();
  $patient->loadRefsAffectations();
  $patient->loadRefsConsultations();
  $patient->loadRefsSejours();
  $patient->loadIPP();
  foreach($patient->_ref_sejours as &$sejour) {
    $sejour->loadRefDossierMedical();
    $sejour->_ref_dossier_medical->updateFormFields();
    $sejour->_ref_dossier_medical->loadRefsAntecedents();
    $sejour->_ref_dossier_medical->loadRefsTraitements();
    $sejour->loadRefsAffectations();
    $sejour->loadNumDossier();
  }
  
  //Affectation
  $affectation =& $patient->_ref_curr_affectation;
  if ($affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    $affectation =& $patient->_ref_next_affectation;
    if ($affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
  // Consultation
  foreach ($patient->_ref_consultations as &$consult) {
    $consult->loadRefsFwd(); //loadRefs();
    $consult->loadRefConsultAnesth();
    $consult->_ref_chir->loadRefFunction();
  }

  // Sejours
  foreach ($patient->_ref_sejours as &$sejour) {
    $sejour->loadExtDiagnostics();
    $sejour->loadRefs();
    $sejour->loadHprimFiles();
    $sejour->loadRefGHM();
    $sejour->loadNumDossier();
    $sejour->canRead();
    $sejour->canEdit();
    foreach ($sejour->_ref_operations as &$operation) {
      $operation->loadRefsFwd();
      $operation->loadHprimFiles();
      $operation->countDocItems();
      $operation->loadRefsActesCCAM();
      $operation->canRead();
      $operation->canEdit();
      foreach ($operation->_ref_actes_ccam as &$acte) {
        $acte->loadRefsFwd();
        $acte->guessAssociation();
      }
      if($operation->plageop_id) {
        $plage =& $operation->_ref_plageop;
        $plage->loadRefsFwd();
      }
      
      $consultAnest =& $operation->_ref_consult_anesth;
      if ($consultAnest->consultation_anesth_id) {
        $consultAnest->loadRefsFwd();
        $consultAnest->_ref_plageconsult->loadRefsFwd();
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("hprim21installed", CModule::getActive("hprim21"));

$smarty->assign("patient" , $patient );
$smarty->assign("listPrat", $listPrat);


$smarty->display("vw_dossier.tpl");

?>