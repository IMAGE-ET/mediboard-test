<?php 

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$patient_id      = CValue::get("patient_id");
$check_collision = CValue::get("check_collision");
$collision_sejour = null;

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();

foreach($patient->_ref_sejours as $_sejour) {  
  $_sejour->loadNumDossier();
  $_sejour->loadRefPraticien();
}

if ($check_collision) {
  $sejour_id = CValue::get("sejour_id");
  $sejour = new CSejour;
  
  if (!$sejour_id) {
    $sejour->patient_id = $patient_id;
    $sejour->group_id = CGroups::$_ref_current->_id;
  }
  else {
    $sejour->load($sejour_id);
  }
  
	// Simulation du formulaire
  $sejour->_date_entree_prevue = CValue::get("date_entree_prevue");
  $sejour->_date_sortie_prevue = CValue::get("date_sortie_prevue");
  $sejour->_hour_entree_prevue = CValue::get("hour_entree_prevue");
  $sejour->_hour_sortie_prevue = CValue::get("hour_sortie_prevue");
  $sejour->_min_entree_prevue = CValue::get("min_entree_prevue");
  $sejour->_min_sortie_prevue = CValue::get("min_sortie_prevue");
  $sejour->updateDBFields();

  // Calcul des collisions potentielles
  $sejours_collides = $sejour->getCollisions();
  foreach($patient->_ref_sejours as $_sejour)
    if (array_key_exists($_sejour->_id, $sejours_collides)) {
      $collision_sejour = $_sejour->_id;
      break;
    }
}

$smarty = new CSmartyDP;

$smarty->assign("patient"         , $patient);
$smarty->assign("collision_sejour", $collision_sejour);

$smarty->display("inc_list_sejours.tpl");
?>