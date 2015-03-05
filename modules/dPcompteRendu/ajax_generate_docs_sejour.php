<?php

/**
 * Génération en masse de documents pour un séjour
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

CApp::setTimeLimit(300);

// On libère la session afin de ne pas bloquer l'utilisateur
CSessionHandler::writeClose();

$modele_id   = CValue::post("modele_id");
$sejours_ids = CValue::post("sejours_ids");

// Chargement des séjours
$sejour = new CSejour();

$where = array();
$where["sejour_id"] = "IN ($sejours_ids)";

$sejours = $sejour->loadList($where);
/** @var CPatient[] $patients */
$patients = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CStoredObject::massLoadFwdRef($sejours, "praticien_id");

/** @var $sejours CSejour[] */
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPatient();
  $_sejour->loadRefPraticien();
}

CSejour::massLoadNDA($sejours);
CPatient::massLoadIPP($patients);

CStoredObject::massCountBackRefs($sejours, "affectations");
CStoredObject::massCountBackRefs($sejours, "consultations");
CStoredObject::massCountBackRefs($sejours, "files");

// Tri par nom de patient
$sorter = CMbArray::pluck($sejours, "_ref_patient", "nom");
array_multisort($sorter, SORT_ASC, $sejours);

// Chargement du modèle
$modele = new CCompteRendu();
$modele->load($modele_id);
$modele->loadContent();

$source = $modele->generateDocFromModel();

$nbDoc = array();

foreach ($sejours as $_sejour) {
  $compte_rendu = new CCompteRendu();
  $compte_rendu->setObject($_sejour);
  $compte_rendu->nom = $modele->nom;
  $compte_rendu->modele_id = $modele->_id;
  $compte_rendu->margin_top = $modele->margin_top;
  $compte_rendu->margin_bottom = $modele->margin_bottom;
  $compte_rendu->margin_left = $modele->margin_left;
  $compte_rendu->margin_right = $modele->margin_right;
  $compte_rendu->page_height = $modele->page_height;
  $compte_rendu->page_width = $modele->page_width;
  $compte_rendu->fast_edit = $modele->fast_edit;
  $compte_rendu->fast_edit_pdf = $modele->fast_edit_pdf;
  $compte_rendu->private = $modele->private;
  $compte_rendu->_source = $source;
  $compte_rendu->factory = $modele->factory;

  $templateManager = new CTemplateManager();
  $templateManager->isModele = false;
  $templateManager->document = $source;
  $_sejour->fillTemplate($templateManager);
  $templateManager->applyTemplate($compte_rendu);
  $compte_rendu->_source = $templateManager->document;

  if ($msg = $compte_rendu->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    continue;
  }
  $nbDoc[$compte_rendu->_id] = 1;
}

echo CApp::fetch("dPcompteRendu", "print_docs", array("nbDoc" => $nbDoc));
