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
$object_class    = mbGetValueFromGetOrSession("object_class");
$object_id       = mbGetValueFromGetOrSession("object_id");

// Liste des alertes
$alertesAllergies    = array();
$alertesInteractions = array();
$alertesIPC          = array();
$alertesProfil       = array();

// Chargement de la catégorie demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);
$listProduits = array();
if(!$prescription->_id) {
  $prescription->object_class = $object_class;
  $prescription->object_id    = $object_id;
} else {
  // Liste des médicaments les plus prescrits
  $favoris = CBcbProduit::getFavoris($prescription->praticien_id);
  foreach($favoris as $curr_fav) {
    $produit = new CBcbProduit();
    $produit->load($curr_fav["code_cip"]);
    $listProduits[] = $produit;
  }
}
if($prescription->object_id) {
  $prescription->loadRefsFwd();
  $prescription->_ref_object->loadRefSejour();
  $prescription->_ref_object->loadRefPatient();
  $prescription->_ref_object->_ref_patient->loadRefDossierMedical();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->updateFormFields();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAntecedents();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsTraitements();
  $prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAddictions();
  $prescription->_ref_object->loadRefsPrescriptions();
  $prescription->loadRefsLines();
  
  // Calcul des alertes
  $allergies    = new CBcbControleAllergie();
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($prescription->_ref_object->_ref_patient);
  foreach($prescription->_ref_prescription_lines as &$line) {
    // Chargement de la posologie
    $line->_ref_produit->loadRefPosologies();
    // Ajout des produits pour les alertes
    $allergies->addProduit($line->code_cip);
    $interactions->addProduit($line->code_cip);
    $IPC->addProduit($line->code_cip);
    $profil->addProduit($line->code_cip);
  }
  $alertesAllergies    = $allergies->getAllergies();
  $alertesInteractions = $interactions->getInteractions();
  $alertesIPC          = $IPC->getIPC();
  $alertesProfil       = $profil->getProfil();
  foreach($prescription->_ref_prescription_lines as &$line) {
    $line->checkAllergies($alertesAllergies);
    $line->checkInteractions($alertesInteractions);
    $line->checkIPC($alertesIPC);
    $line->checkProfil($alertesProfil);
  }
}

// Liste des praticiens
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("httpreq", 1);

$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);

$smarty->assign("prescription", $prescription);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("listProduits", $listProduits);

$smarty->display("inc_vw_prescription.tpl");

?>