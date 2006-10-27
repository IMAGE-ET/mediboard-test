<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$pat_id = mbGetValueFromGetOrSession("pat_id");

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);


// Chargement du dossier patient
$patient = new CPatient;
$patient->load($pat_id);

if ($patient->patient_id) {
	//$patient->loadDossierComplet();
  $patient->loadRefsConsultations();
  $patient->loadRefsSejours();
  $patient->loadRefsAntecedents();
  $patient->loadRefsTraitements();
  $patient->loadRefsAffectations();
  
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
  foreach ($patient->_ref_consultations as $keyConsult => $valueConsult) {
    $consult =& $patient->_ref_consultations[$keyConsult];
    $consult->loadRefsFwd(); //loadRefs();
  }

  // Sejours
  foreach ($patient->_ref_sejours as $keySejour => $valueSejour) {
    $sejour =& $patient->_ref_sejours[$keySejour];
    $sejour->loadRefs();
    $sejour->loadRefGHM();
    foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
      $operation =& $sejour->_ref_operations[$keyOp];
      $operation->loadRefsFwd();
      
      $operation->getNumDocsAndFiles();
      $operation->loadRefsActesCCAM();
      foreach ($operation->_ref_actes_ccam as $keyActe => $valueActe) {
        $acte =& $operation->_ref_actes_ccam[$keyActe];
        $acte->loadRefsFwd();
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

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();


// Création du template
$smarty = new CSmartyDP(1);

$smarty->debugging = false;

$smarty->assign("patient"       , $patient       );
$smarty->assign("listPrat"      , $listPrat      );
$smarty->assign("canEditCabinet", $canEditCabinet);


$smarty->display("vw_dossier.tpl");

?>