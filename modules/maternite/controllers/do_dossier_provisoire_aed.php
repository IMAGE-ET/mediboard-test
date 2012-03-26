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

$operation_id = CValue::post("operation_id");

$operation = new COperation();
$operation->load($operation_id);

$sejour      = $operation->loadRefSejour();
$parturiente = $operation->loadRefPatient();
$grossesse   = $sejour->loadRefGrossesse();
$curr_affect = $sejour->loadRefCurrAffectation();

function storeObject($object) {
  $title = $object->_id ? "-msg-modify" : "-msg-create";
  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    echo CAppUI::getMsg();
    CApp::rip();
  }
  
  CAppUI::setMsg(CAppUI::tr(get_class($object) . $title), UI_MSG_OK);
}

$terme_prevu = $grossesse->terme_prevu;

// Création de la naissance provisoire:
//   1. Créer le nouveau patient (bébé)
//   2. Créer le séjour du patient
//   3. Créer la naissance

$patient = new CPatient;
$patient->nom = $parturiente->nom;
$patient->prenom = "provi";
$patient->naissance = $terme_prevu;
storeObject($patient);

$sejour_enfant = new CSejour;
$sejour_enfant->patient_id = $patient->_id;
$sejour_enfant->entree_prevue = mbDateTime();
$sejour_enfant->sortie_prevue = $sejour->sortie;
$sejour_enfant->praticien_id = $sejour->praticien_id;
$sejour_enfant->group_id = $sejour->group_id;
storeObject($sejour_enfant);

$patient->prenom = $sejour_enfant->_id;
$patient->store();

$naissance = new CNaissance;
$naissance->operation_id = $operation_id;
$naissance->grossesse_id = $grossesse->_id;
$naissance->sejour_enfant_id = $sejour_enfant->_id;
storeObject($naissance);

echo CAppUI::getMsg();

CApp::rip();
?>