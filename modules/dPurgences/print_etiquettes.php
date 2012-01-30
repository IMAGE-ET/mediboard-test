<?php /* $Id: print_etiquettes.php $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

ignore_user_abort(true);

// Chargement du rpu
$rpu_id = CValue::get("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

// Chargement du patient
$rpu->_ref_sejour->loadRefPatient();

// Chargement des modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette;
$where = array();
$where['object_class'] = " = 'CRPU'";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

// Récupération des valeurs des champs;
$fields = $rpu->_ref_sejour->completeLabelFields();
$fields = array_merge($fields, $rpu->_ref_sejour->_ref_patient->completeLabelFields());
$fields = array_merge($fields, $modele_etiquette->completeLabelFields());

if (count($modeles_etiquettes = $modele_etiquette->loadList($where))) {
	// TODO: faire une modale pour proposer les modèles d'étiquettes
	$first_modele = reset($modeles_etiquettes);
	$first_modele->replaceFields($fields);
	$first_modele->printEtiquettes();
}
?>