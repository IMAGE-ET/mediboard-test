<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$modele_id = CValue::get("modele_id");
$sejours_ids = CValue::get("sejours_ids");

// Chargement des s�jours
$sejour = new CSejour;

$where = array();
$where["sejour_id"] = "IN ($sejours_ids)";

$sejours = $sejour->loadList($where);

CMbObject::massLoadFwdRef($sejours, "patient_id");

foreach ($sejours as $_sejour) {
  $_sejour->loadRefPatient();
}

// Tri par nom de patient
$sorter = CMbArray::pluck($sejours, "_ref_patient", "nom");
array_multisort($sorter, SORT_ASC, $sejours);

// Chargement du mod�le
$modele = new CCompteRendu();
$modele->load($modele_id);
$modele->loadContent();

$source = $modele->generateDocFromModel();

$nbDoc = array();

foreach ($sejours as $_sejour) {
  $compte_rendu = new CCompteRendu;
  $compte_rendu->object_class = "CSejour";
  $compte_rendu->object_id = $_sejour->_id;
  $compte_rendu->nom = $modele->nom;
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
  
  $templateManager = new CTemplateManager;
  $templateManager->isModele = false;
  $_sejour->fillTemplate($templateManager);
  $templateManager->applyTemplate($compte_rendu);
  $compte_rendu->_source = $templateManager->document;

  if ($msg = $compte_rendu->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    $nbDoc[$compte_rendu->_id] = 1;
  }
}

echo CApp::fetch("dPcompteRendu", "print_docs", array("nbDoc" => $nbDoc));

?>