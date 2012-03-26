<?php /* $Id: print_etiquettes.php $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

ignore_user_abort(true);

// Chargement du rpu
$sejour_id = CValue::get("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du patient
$sejour->loadRefPatient();

// Chargement des modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette;
$where = array();
$where['object_class'] = " = 'CSejour'";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

// Récupération des valeurs des champs;
$fields = $sejour->completeLabelFields();
$fields = array_merge($fields, $sejour->_ref_patient->completeLabelFields());

if (count($modeles_etiquettes = $modele_etiquette->loadList($where))) {
  // TODO: faire une modale pour proposer les modèles d'étiquettes
  $first_modele = reset($modeles_etiquettes);
  $first_modele->replaceFields($fields);
  $first_modele->printEtiquettes();
}

?>