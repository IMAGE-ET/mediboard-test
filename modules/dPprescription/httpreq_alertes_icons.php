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

// Chargement de la prescription demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Liste des alertes
$alertesAllergies    = 0;
$alertesInteractions = 0;
$alertesIPC          = 0;
$alertesProfil       = 0;

if($prescription->_id) {
  $prescription->loadRefsFwd();
  $prescription->_ref_object->loadRefSejour();
  $prescription->_ref_object->loadRefPatient();
  $prescription->_ref_object->_ref_patient->loadRefDossierMedical();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->updateFormFields();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAntecedents();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsTraitements();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAddictions();
  $prescription->_ref_object->loadRefsPrescriptions();
  
  // Chargement des lignes
  $prescription->loadRefsLines();
  $allergies    = new CBcbControleAllergie();
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($prescription->_ref_object->_ref_patient);
  foreach($prescription->_ref_prescription_lines as &$line) {
    // Prise en compte pour les alertes
    $allergies->addProduit($line->code_cip);
    $interactions->addProduit($line->code_cip);
    $IPC->addProduit($line->code_cip);
    $profil->addProduit($line->code_cip);
  }
  // Calcul du nombre d'alertes
  $alertesAllergies    = $allergies->getAllergies();
  $alertesInteractions = $interactions->testInteractions();
  $alertesIPC          = $IPC->testIPC();
  $alertesProfil       = $profil->testProfil();
}

// Liste des praticiens
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_EDIT);


// Création du template
$smarty = new CSmartyDP();


$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);

$smarty->display("inc_alertes_icons.tpl");

?>