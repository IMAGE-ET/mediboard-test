<?php

/**
 * Création d'un dossier de naissance
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$naissance_id      = CValue::post("naissance_id");
$operation_id      = CValue::post("operation_id");
$patient_id        = CValue::post("patient_id");
$praticien_id      = CValue::post("praticien_id");
$hors_etab         = CValue::post("hors_etab");
$sexe              = CValue::post("sexe");
$heure             = CValue::post("_heure");
$date_time         = CValue::post("date_time");
$rang              = CValue::post("rang");
$date_naissance    = CValue::post("naissance");
$nom               = CValue::post("nom");
$prenom            = CValue::post("prenom");
$poids             = CValue::post("poids");
$taille            = CValue::post("taille");
$num_naissance     = CValue::post("num_naissance");
$fausse_couche     = CValue::post("fausse_couche");
$rques             = CValue::post("rques");
$constantes_id     = CValue::post("constantes_medicales_id");
$sejour_maman_id   = CValue::post("sejour_maman_id");
$perimetre_cranien = CValue::post("perimetre_cranien");
$callback          = CValue::post("callback");

$sejour = new CSejour();
$sejour->load($sejour_maman_id);

$parturiente = $sejour->loadRefPatient();
$grossesse   = $sejour->loadRefGrossesse();
$curr_affect = $sejour->loadRefCurrAffectation();

$datetime = CMbDT::dateTime();
$date     = CMbDT::date($datetime);

/**
 * Fonction utilitaire pour la sauvegarde rapide d'un object avec génération du message
 *
 * @param CMbObject $object Objet à enregister
 *
 * @return void
 */
function storeObject($object) {
  $title = $object->_id ? "-msg-modify" : "-msg-create";
  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    echo CAppUI::getMsg();
    // Il peut y avoir un msg de retour postérieur à la création de l'objet
    // On continue donc le processus de création de la naissance
    //CApp::rip();
  }
  
  CAppUI::setMsg(CAppUI::tr(get_class($object) . $title), UI_MSG_OK);
}

// Cinq étapes pour la création de la naissance :
//   1. Créer le nouveau patient (enfant)
//   2. Créer le séjour de l'enfant
//   3. Créer le relevé de constantes
//   4. Créer l'affectation du séjour
//   5. Créer la naissance

if (!$naissance_id) {
  // Etape 1 (patient)
  $patient = new CPatient();
  $patient->nom = $nom;
  $patient->prenom = $prenom;
  $patient->sexe = $sexe;
  $patient->civilite = "enf";
  $patient->naissance = $date;
  $patient->_naissance = true;
  storeObject($patient);

  // Etape 2 (séjour)
  $sejour_enfant = new CSejour();
  $sejour_enfant->entree_reelle = "$date $heure";
  $sejour_enfant->sortie_prevue = $curr_affect->sortie ? $curr_affect->sortie : $sejour->sortie;
  $sejour_enfant->patient_id = $patient->_id;
  $sejour_enfant->praticien_id = $praticien_id;
  $sejour_enfant->group_id = $sejour->group_id;
  $sejour_enfant->_naissance = true;
  storeObject($sejour_enfant);

  // Etape 3 (constantes)
  if ($poids || $taille || $perimetre_cranien) {
    $constantes = new CConstantesMedicales();
    $constantes->patient_id = $patient->_id;
    $constantes->context_class = $sejour_enfant->_class;
    $constantes->context_id = $sejour_enfant->_id;
    $constantes->datetime = "now";
    $constantes->poids = $poids;
    $constantes->taille = $taille;
    $constantes->perimetre_cranien = $perimetre_cranien;
    storeObject($constantes);
  }

  // Etape 4 (affectation)
  // Checker si l'affectation de la maman existe
  if ($heure && $curr_affect->_id) {
    $affectation = new CAffectation();
    $affectation->entree = $sejour_enfant->entree_reelle;
    $affectation->sortie = $sejour_enfant->sortie_prevue;
    $affectation->lit_id = $curr_affect->lit_id;
    $affectation->service_id = $curr_affect->service_id;
    $affectation->sejour_id = $sejour_enfant->_id;
    $affectation->parent_affectation_id = $curr_affect->_id;
    storeObject($affectation);
  }

  // Etape 5 (naissance)
  $naissance = new CNaissance();
  $naissance->sejour_maman_id   = $sejour_maman_id;
  $naissance->sejour_enfant_id  = $sejour_enfant->_id;
  $naissance->operation_id      = $operation_id;
  $naissance->grossesse_id      = $grossesse->_id;
  $naissance->rang              = $rang;
  $naissance->_heure            = $heure;
  $naissance->date_time         = "$date $heure";
  $naissance->hors_etab         = $hors_etab;
  $naissance->num_naissance     = $num_naissance;
  $naissance->fausse_couche     = $fausse_couche;
  $naissance->rques             = $rques;
  storeObject($naissance);
}
// Modification d'une naissance
else {
  $validation_naissance = false;
  $naissance = new CNaissance();
  $naissance->load($naissance_id);
  $naissance->rang              = $rang;
  $naissance->hors_etab         = $hors_etab;
  $naissance->num_naissance     = $num_naissance;
  $naissance->fausse_couche     = $fausse_couche;
  $naissance->rques             = $rques;

  if (!$naissance->date_time && $heure) {
    $validation_naissance = true;
    $naissance->operation_id = $operation_id;
  }
  
  $naissance->date_time = $date_time;
  storeObject($naissance);
  
  $sejour = $naissance->loadRefSejourEnfant();
  
  $patient = new CPatient();
  $patient->load($sejour->patient_id);
  $patient->nom = $nom;
  $patient->prenom = $prenom;
  $patient->nom = $nom;
  $patient->sexe = $sexe;
  $patient->naissance = $date_naissance;
  storeObject($patient);
  
  $sejour_enfant = new CSejour();
  $sejour_enfant->load($naissance->sejour_enfant_id);
  $sejour_enfant->praticien_id = $praticien_id;
  $sejour_enfant->_naissance = true;
  storeObject($sejour_enfant);
  
  // Effectuer l'admission si nécessaire (si issu d'un dossier provisoire)
  if ($validation_naissance) {
    
    // On passe la date de naissance du bébé au jour courant
    $patient->naissance = CMbDT::date();
    storeObject($patient);
    
    // L'entrée réelle du séjour à maintenant
    $sejour_enfant->entree_reelle = $datetime;
    storeObject($sejour_enfant);
    
    // Checker également si l'affectation de la maman existe
    // Et dans ce cas, la créer pour le bébé  
    if ($curr_affect->_id) {
      $affectation = $sejour_enfant->loadRefCurrAffectation();
      
      if (!$affectation->_id) {
        $affectation = new CAffectation();
        $affectation->entree = $sejour_enfant->entree_reelle;
        $affectation->sortie = $sejour_enfant->sortie_prevue;
        $affectation->lit_id = $curr_affect->lit_id;
        $affectation->sejour_id = $sejour_enfant->_id;
        $affectation->parent_affectation_id = $curr_affect->_id;
        storeObject($affectation);
      }
    }
  }
  
  if ($poids || $taille || $perimetre_cranien) {
    $constantes = new CConstantesMedicales();
    $constantes->load($constantes_id);
    $constantes->poids = $poids;
    $constantes->taille = $taille;
    $constantes->perimetre_cranien = $perimetre_cranien;
    
    // Depuis un dossier provisoire, les constantes médicales ne sont pas créées.
    if (!$constantes->_id) {
      $constantes->context_class = $sejour_enfant->_class;
      $constantes->context_id = $sejour_enfant->_id;
      $constantes->patient_id = $patient->_id;
      $constantes->datetime = CMbDT::dateTime();
    }
    
    storeObject($constantes);
  }
}
echo CAppUI::getMsg();

if ($callback) {
  CAppUI::callbackAjax($callback);
}

CApp::rip();
