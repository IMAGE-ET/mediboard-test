<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

ignore_user_abort(true);

$printer_id   = CValue::get("printer_id");
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$modele_etiquette_id = CValue::get("modele_etiquette_id");
$params       = CValue::get("params", array());

$object = new $object_class;
$object->load($object_id);

$fields = array();

$object->completeLabelFields($fields, $params);

// Chargement des modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette();
$modele_etiquette->load($modele_etiquette_id);

if ($modele_etiquette->_id) {
  $modele_etiquette->completeLabelFields($fields, $params);
  $modele_etiquette->replaceFields($fields);
  $modele_etiquette->printEtiquettes($printer_id);
  CApp::rip();
}

$where = array();

$where['object_class'] = " = '$object_class'";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

if (count($modeles_etiquettes = $modele_etiquette->loadList($where))) {
  // TODO: faire une modale pour proposer les modèles d'étiquettes
  $first_modele = reset($modeles_etiquettes);
  $first_modele->completeLabelFields($fields, $params);
  $first_modele->replaceFields($fields);
  $first_modele->printEtiquettes($printer_id);
}
else {
  CAppUI::stepAjax("Aucun modèle d'étiquette configuré pour l'objet " . CAppUI::tr($object_class));
}
