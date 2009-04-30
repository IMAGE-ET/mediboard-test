<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPlanningOp
 * @version $Revision: 6171 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$patient_id = mbGetValueFromGet("patient_id");
$sejour_id  = mbGetValueFromGet("sejour_id");

$sejour = new CSejour;
$sejour->load($sejour_id);

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();
  
$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
  $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}

foreach($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->display("inc_check_correspondant_medical.tpl");

?>