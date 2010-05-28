<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

$pat_id    = CValue::getOrSession("pat_id");
$sejour_id = CValue::getOrSession("sejour_id");

// Chargement du dossier patient
$patient = new CPatient;
$patient->load($pat_id);

$sejour = new CSejour;

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

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
  foreach ($patient->_ref_consultations as $consult) {
    $consult->loadRefsFwd(); //loadRefs();
    $consult->loadRefConsultAnesth();
    $consult->_ref_chir->loadRefFunction();
  }

  // Sejours
  foreach ($patient->_ref_sejours as $_sejour) {
    $_sejour->loadRefDossierMedical();
    $_sejour->_ref_dossier_medical->updateFormFields();
    $_sejour->_ref_dossier_medical->loadRefsAntecedents();
    $_sejour->_ref_dossier_medical->loadRefsTraitements();
    $_sejour->loadRefsAffectations();
    $_sejour->loadExtDiagnostics();
    $_sejour->loadRefs();
    $_sejour->countEchangeHprim();
    $_sejour->loadRefGHM();
    $_sejour->loadNumDossier();
    $_sejour->canRead();
    $_sejour->canEdit();
    foreach ($_sejour->_ref_operations as $_operation) {
      $_operation->loadRefsFwd();
      $_operation->countEchangeHprim();
      $_operation->countDocItems();
      $_operation->loadRefsActesCCAM();
      $_operation->canRead();
      $_operation->canEdit();
      foreach ($_operation->_ref_actes_ccam as $_acte) {
        $_acte->loadRefsFwd();
        $_acte->guessAssociation();
      }
      if($_operation->plageop_id) {
        $plage =& $_operation->_ref_plageop;
        $plage->loadRefsFwd();
      }
      
      $consultAnest =& $_operation->_ref_consult_anesth;
      if ($consultAnest->consultation_anesth_id) {
        $consultAnest->loadRefsFwd();
        $consultAnest->_ref_plageconsult->loadRefsFwd();
      }
    }
    
    if ($_sejour->_id == $sejour_id) {
      $sejour = $_sejour;
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
//$smarty->assign("sejour"  , $sejour  );
$smarty->assign("listPrat", $listPrat);

$smarty->display("vw_dossier.tpl");

?>