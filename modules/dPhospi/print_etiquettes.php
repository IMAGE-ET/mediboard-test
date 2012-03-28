<?php

/**
 * aphmOdonto
 *  
 * @category aphmOdonto
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

ignore_user_abort(true);

$printer_id   = CValue::get("printer_id");
$rpu_id       = CValue::get("rpu_id");
$sejour_id    = CValue::get("sejour_id");
$object_class = CValue::get("object_class");

// Chargement du rpu
if ($rpu_id) {
  $rpu = new CRPU();
  $rpu->load($rpu_id);
  $sejour = $rpu->loadRefSejour();
}

if ($sejour_id) {
  $sejour = new CSejour;
  $sejour->load($sejour_id);
}

$patient = $sejour->loadRefPatient();

// Chargement des modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette;

$where = array();

$where['object_class'] = " = '$object_class'";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

$fields = $sejour->completeLabelFields();
$fields = array_merge($fields, $patient->completeLabelFields());
$fields = array_merge($fields, $modele_etiquette->completeLabelFields());

if (count($modeles_etiquettes = $modele_etiquette->loadList($where))) {
  // TODO: faire une modale pour proposer les modèles d'étiquettes
  $first_modele = reset($modeles_etiquettes);
  $first_modele->replaceFields($fields);
  $first_modele->printEtiquettes($printer_id);
}
else {
  CAppUI::stepAjax("Aucun modèle d'étiquette configuré pour l'objet " . CAppUI::tr($object_class));
}
?>