<?php 

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$patient_id      = CValue::get("patient_id");
$sejour_id       = CValue::get("sejour_id");
$check_collision = CValue::get("check_collision");
$date_entree_prevue = CValue::get("date_entree_prevue");
$hour_entree_prevue = CValue::get("hour_entree_prevue");
$min_entree_prevue  = CValue::get("min_entree_prevue");
$collision_sejour = null;

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();

if (!$patient->_id) {
  CAppUI::stepMessage(UI_MSG_WARNING, "Patient '%s' inexistant", $patient_id);
  return;
}

$date = $date_entree_prevue;
$date .= " ".str_pad($hour_entree_prevue, 2, "0", STR_PAD_LEFT);
$date .= ":".str_pad($min_entree_prevue, 2, "0", STR_PAD_LEFT);
$date .= ":00";

foreach($patient->_ref_sejours as $_sejour) {
  // Séjours proches
  if ($_sejour->sortie) {
    if (mbDateTime("+". CAppUI::conf("dPplanningOp CSejour hours_sejour_proche") ."HOUR", $_sejour->sortie) > $date && $date > $_sejour->sortie) {
      $_sejour->_is_proche = 1;
    }
  }
  $_sejour->loadNDA();
  $_sejour->loadRefPraticien();
  $_sejour->loadRefEtablissement();
}

if ($check_collision) {
  $sejour = new CSejour;
  
  if (!$sejour_id) {
    $sejour->patient_id = $patient_id;
    $sejour->group_id = CGroups::$_ref_current->_id;
  }
  else {
    $sejour->load($sejour_id);
  }
  
  // Simulation du formulaire
  $sejour->_date_entree_prevue = $date_entree_prevue;
  $sejour->_date_sortie_prevue = CValue::get("date_sortie_prevue");
  $sejour->_hour_entree_prevue = $hour_entree_prevue;
  $sejour->_hour_sortie_prevue = CValue::get("hour_sortie_prevue");
  $sejour->_min_entree_prevue  = $min_entree_prevue;
  $sejour->_min_sortie_prevue  = CValue::get("min_sortie_prevue");
  $sejour->updatePlainFields();

  // Calcul des collisions potentielles
  $sejours_collides = $sejour->getCollisions();
  foreach($patient->_ref_sejours as $_sejour) {
    
    if (array_key_exists($_sejour->_id, $sejours_collides)) {
      $collision_sejour = $_sejour->_id;
      break;
    }
  }
}

$smarty = new CSmartyDP;

$smarty->assign("patient"         , $patient);
$smarty->assign("collision_sejour", $collision_sejour);

$smarty->display("../../dPplanningOp/templates/inc_list_sejours.tpl");
?>