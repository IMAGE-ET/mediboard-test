<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$naissance_id = CValue::post("naissance_id");
$operation_id = CValue::post("operation_id");
$patient_id   = CValue::post("patient_id");
$praticien_id = CValue::post("praticien_id");
$hors_etab    = CValue::post("hors_etab");
$sexe         = CValue::post("sexe");
$heure        = CValue::post("heure");
$rang         = CValue::post("rang");
$date_naissance = CValue::post("naissance");
$nom          = CValue::post("nom");
$prenom       = CValue::post("prenom");
$poids        = CValue::post("poids");
$taille       = CValue::post("taille");
$constantes_id = CValue::post("constantes_medicales_id");
$sejour_maman_id = CValue::post("sejour_maman_id");
$perimetre_cranien = CValue::post("perimetre_cranien");

$sejour = new CSejour;
$sejour->load($sejour_maman_id);

$parturiente = $sejour->loadRefPatient();
$grossesse   = $sejour->loadRefGrossesse();
$curr_affect = $sejour->loadRefCurrAffectation();

$datetime = mbDateTime();
$date     = mbDate($datetime);

function storeObject($object) {
  $title = $object->_id ? "-msg-modify" : "-msg-create";
  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    echo CAppUI::getMsg();
    CApp::rip();
  }
  
  CAppUI::setMsg(CAppUI::tr(get_class($object) . $title), UI_MSG_OK);
}

// Cinq étapes pour la création de la naissance :
//   1. Créer le nouveau patient (enfant)
//   2. Créer le relevé de constantes
//   3. Créer le séjour de l'enfant
//   4. Créer l'affectation du séjour
//   5. Créer la naissance

if (!$naissance_id) {
  // Etape 1 (patient)
  $patient = new CPatient;
  
  $patient->nom = $nom;
  $patient->prenom = $prenom;
  $patient->sexe = $sexe;
  $patient->naissance = $date;
  storeObject($patient);
  
  // Etape 2 (constantes)
  if ($poids || $taille || $perimetre_cranien) {
    $constantes = new CConstantesMedicales;
    $constantes->patient_id = $patient->_id;
    $constantes->datetime = "now";
    $constantes->poids = $poids;
    $constantes->taille = $taille;
    $constantes->perimetre_cranien = $perimetre_cranien;
    storeObject($constantes);
  }
  
  // Etape 3 (séjour)
  $sejour_enfant = new CSejour;
  
  // Si dossier provisoire, entrée prévue
  if ($heure) {
    $sejour_enfant->entree_reelle = $datetime;
  }
  else {
    $sejour_enfant->entree_prevue = mbDate();
  }
  
  $sejour_enfant->sortie_prevue = $curr_affect->sortie ? $curr_affect->sortie : $sejour->sortie;
  $sejour_enfant->patient_id = $patient->_id;
  $sejour_enfant->praticien_id = $praticien_id;
  $sejour_enfant->group_id = $sejour->group_id;
  storeObject($sejour_enfant);
  
  // Etape 4 (affectation)
  // Sauf si c'est un dossier provisoire
  // Checker également si l'affectation de la maman existe
  if ($heure && $curr_affect->_id) {
    $affectation = new CAffectation;
    $affectation->entree = $sejour_enfant->entree_reelle;
    $affectation->sortie = $sejour_enfant->sortie_prevue;
    $affectation->lit_id = $curr_affect->lit_id;
    $affectation->sejour_id = $sejour_enfant->_id;
    $affectation->parent_affectation_id = $curr_affect->_id;
    storeObject($affectation);
  }
  
  // Etape 5 (naissance)
  $naissance = new CNaissance;
  $naissance->sejour_maman_id  = $sejour_maman_id;
  $naissance->sejour_enfant_id = $sejour_enfant->_id;
  $naissance->operation_id = $operation_id;
  $naissance->grossesse_id = $grossesse->_id;
  $naissance->rang         = $rang;
  $naissance->heure        = $heure;
  $naissance->hors_etab    = $hors_etab;
  storeObject($naissance);
}
// Modification d'une naissance
else {
  $validation_naissance = false;
  $naissance = new CNaissance;
  $naissance->load($naissance_id);
  $naissance->rang = $rang;
  $naissance->hors_etab = $hors_etab;
  
  if (!$naissance->heure && $heure) {
    $validation_naissance = true;
    $naissance->operation_id = $operation_id;
  }
  
  $naissance->heure = $heure;
  storeObject($naissance);
  
  $sejour = $naissance->loadRefSejourEnfant();
  
  $patient = new CPatient;
  $patient->load($sejour->patient_id);
  $patient->nom = $nom;
  $patient->prenom = $prenom;
  $patient->nom = $nom;
  $patient->sexe = $sexe;
  $patient->naissance = $date_naissance;
  storeObject($patient);
  
  $sejour_enfant = new CSejour;
  $sejour_enfant->load($naissance->sejour_enfant_id);
  $sejour_enfant->praticien_id = $praticien_id;
  storeObject($sejour_enfant);
  
  // Créer l'affectation si nécessaire (si issu d'un dossier provisoire)
  // Checker également si l'affectation de la maman existe
  if ($validation_naissance && $curr_affect->_id) {
    $sejour_enfant->entree_reelle = $datetime;
    storeObject($sejour_enfant);
    
    $affectation = $sejour_enfant->loadRefCurrAffectation();
    
    if (!$affectation->_id) {
      $affectation = new CAffectation;
      $affectation->entree = $sejour_enfant->entree_reelle;
      $affectation->sortie = $sejour_enfant->sortie_prevue;
      $affectation->lit_id = $curr_affect->lit_id;
      $affectation->sejour_id = $sejour_enfant->_id;
      $affectation->parent_affectation_id = $curr_affect->_id;
      storeObject($affectation);
    }
  }
  
  if ($poids || $taille || $perimetre_cranien) {
    $constantes = new CConstantesMedicales;
    $constantes->load($constantes_id);
    $constantes->poids = $poids;
    $constantes->taille = $taille;
    $constantes->perimetre_cranien = $perimetre_cranien;
    
    // Depuis un dossier provisoire, les constantes médicales ne sont pas créées.
    if (!$constantes->_id) {
      $constantes->patient_id = $patient->_id;
      $constantes->datetime = mbDateTime();
    }
    
    storeObject($constantes);
  }
}
echo CAppUI::getMsg();

CApp::rip();

?>