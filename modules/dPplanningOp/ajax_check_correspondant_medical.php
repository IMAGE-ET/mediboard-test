<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$patient_id   = CValue::get("patient_id");
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

/** @var CMbObject $object */
$object = new $object_class;
$object->load($object_id);

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();
  
$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
  $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}

foreach ($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->display("inc_check_correspondant_medical.tpl");
