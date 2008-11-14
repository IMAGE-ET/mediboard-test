<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");

// Liste des alertes
$alertesAllergies    = array();
$alertesInteractions = array();
$alertesIPC          = array();
$alertesProfil       = array();

// Chargement de la catégorie demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);

if($prescription->object_id) {
  $prescription->loadRefsFwd();
  $object =& $prescription->_ref_object;
  $object->loadRefSejour();
  $object->loadRefPatient();
  $patient =& $object->_ref_patient;
  $patient->loadRefDossierMedical();
  $patient->_ref_dossier_medical->updateFormFields();
  $patient->_ref_dossier_medical->loadRefsAntecedents();
  $patient->_ref_dossier_medical->loadRefsTraitements();
  $object->loadRefsPrescriptions();
  $prescription->loadRefsLinesMed();
  
  // Calcul des alertes
  $allergies    = new CBcbControleAllergie();
  $allergies->setPatient($prescription->_ref_object->_ref_patient);
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($prescription->_ref_object->_ref_patient);
  foreach($prescription->_ref_prescription_lines as &$line) {
    // Ajout des produits pour les alertes
    $allergies->addProduit($line->code_cip);
    $interactions->addProduit($line->code_cip);
    $IPC->addProduit($line->code_cip);
    $profil->addProduit($line->code_cip);
  }
  $prescription->loadRefsPerfusions();
  foreach($prescription->_ref_perfusions as $_perfusion){
    $_perfusion->loadRefsLines();
    foreach($_perfusion->_ref_lines as $_perf_line){
      $allergies->addProduit($_perf_line->code_cip);
      $interactions->addProduit($_perf_line->code_cip);
      $IPC->addProduit($_perf_line->code_cip);
      $profil->addProduit($_perf_line->code_cip);  
    }
  }
  $alertesAllergies    = $allergies->getAllergies();
  $alertesInteractions = $interactions->getInteractions();
  $alertesIPC          = $IPC->getIPC();
  $alertesProfil       = $profil->getProfil();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);

$smarty->display("vw_full_alertes.tpl");

?>