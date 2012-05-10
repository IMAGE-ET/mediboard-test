<?php

/**
 * dPhospi
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$lit_id         = CValue::get("lit_id");
$mode_vue_tempo = CValue::get("mode_vue_tempo");
$date           = CValue::get('date');
$granularite    = CValue::get("granularite", "day");
$readonly       = CValue::get("readonly");
$prestation_id  = CValue::get("prestation_id");
$readonly        = CValue::getOrSession("readonly", 0);

$unite = "";
$period = "";
$datetimes = array();
$change_month = array();
$granularites = array("day", "week", "4weeks");

switch ($granularite) {
  case "day":
    $unite = "hour";
    $nb_unite = 1;
    $nb_ticks = 24;
    $step = "+1 hour";
    $period = "1hour";
    $date_min = mbDateTime($date);
    $date_before = mbDate("-1 day", $date);
    $date_after  = mbDate("+1 day", $date);
    break;
  case "week":
    $unite = "hour";
    $nb_unite = 6;
    $nb_ticks = 28;
    $step = "+6 hours";
    $period = "6hours";
    $date_min = mbDateTime("-2 days", $date);
    $date_before = mbDate("-1 week", $date);
    $date_after = mbDate("+1 week", $date);
    break;
  case "4weeks":
    $unite = "day";
    $nb_unite = 1;
    $nb_ticks = 28;
    $step = "+1 day";
    $period = "1day";
    $date_min = mbDateTime("-1 week", CMbDate::dirac("week", $date));
    $date_before = mbDate("-4 week", $date);
    $date_after = mbDate("+4 week", $date);
}

$current = CMbDate::dirac("hour", mbDateTime());
$offset = $nb_ticks * $nb_unite;

$date_max = mbDateTime("+ $offset $unite", $date_min);
$temp_datetime = mbDateTime(null, $date_min);

for ($i = 0 ; $i < $nb_ticks ; $i++) {
  $offset = $i * $nb_unite;
  
  $datetime = mbDateTime("+ $offset $unite", $date_min);
  $datetimes[] = $datetime;
}

$lit = new CLit;
$lit->load($lit_id);
$lit->loadRefChambre();

$liaisons_items = $lit->loadBackRefs("liaisons_items");
$items_prestations = CMbObject::massLoadFwdRef($liaisons_items, "item_prestation_id");
$prestations_ids = CMbArray::pluck($items_prestations, "object_id");

if (in_array($prestation_id, $prestations_ids)) {
  $inverse = array_flip($prestations_ids);
  $item_prestation = $items_prestations[$inverse[$prestation_id]];
  if ($item_prestation->_id) {
    $lit->_selected_item = $item_prestation;
  }
  else {
    $lit->_selected_item = new CItemPrestation;
  }
}
else {
  $lit->_selected_item = new CItemPrestation;
}

// Chargement des affectations
$where = array();
$where["lit_id"] = "= '$lit_id'";
$where["entree"] = "<= '$date_max'";
$where["sortie"] = ">= '$date_min'";

$affectation = new CAffectation;
$affectations = $affectation->loadList($where);

$sejours  = CMbObject::massLoadFwdRef($affectations, "sejour_id");
$patients = CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");
$operations = array();

$suivi_affectation = false;

foreach ($affectations as $_affectation) {
  if (!$suivi_affectation && $_affectation->parent_affectation_id) {
    $suivi_affectation = true;
  }
  $_affectation->loadRefsAffectations();
  $sejour = $_affectation->loadRefSejour();
  $sejour->loadRefPraticien()->loadRefFunction();
  $patient = $sejour->loadRefPatient();
  $patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical()->loadRefsAntecedents();
  $lit->_ref_affectations[$_affectation->_id] = $_affectation;
  $_affectation->_entree_offset = CMbDate::position(max($date_min, $_affectation->entree), $date_min, $period);
  $_affectation->_sortie_offset = CMbDate::position(min($date_max, $_affectation->sortie), $date_min, $period);
  $_affectation->_width = $_affectation->_sortie_offset - $_affectation->_entree_offset;
  
  if (isset($operations[$sejour->_id])) {
    $_operations = $operations[$sejour->_id];
  }
  else {
    $operations[$sejour->_id] = $_operations = $sejour->loadRefsOperations();
  }
  
  foreach ($_operations as $key=>$_operation) {
    $_operation->loadRefPlageOp(1);
    
    $hour_operation = mbTransformTime(null, $_operation->temp_operation, "%H");
    $min_operation = mbTransformTime(null, $_operation->temp_operation, "%M");
    
    $_operation->_debut_offset[$_affectation->_id] = CMbDate::position($_operation->_datetime, max($date_min, $_affectation->entree), $period);
    
    $_operation->_fin_offset[$_affectation->_id] = CMbDate::position(mbDateTime("+$hour_operation hours +$min_operation minutes",$_operation->_datetime), max($date_min, $_affectation->entree), $period);
    $_operation->_width[$_affectation->_id] = $_operation->_fin_offset[$_affectation->_id] - $_operation->_debut_offset[$_affectation->_id];
    
    if (($_operation->_datetime > $date_max)) {
      $_operation->_width_uscpo[$_affectation->_id] = 0;
    }
    else {
      $fin_uscpo = $hour_operation + 24 * $_operation->duree_uscpo;
      $_operation->_width_uscpo[$_affectation->_id] = CMbDate::position(mbDateTime("+$fin_uscpo hours + $min_operation minutes", $_operation->_datetime), max($date_min, $_affectation->entree), $period) - $_operation->_fin_offset[$_affectation->_id];
    }
  }
}

$intervals = array();
if (isset($lit->_ref_affectations) && count($lit->_ref_affectations)) {
  foreach ($lit->_ref_affectations as $_affectation) {
    $intervals[$_affectation->_id] = array(
      "lower" => $_affectation->entree,
      "upper" => $_affectation->sortie,
    );
  }
  $lit->_lines = CMbRange::rearrange($intervals);
}

$smarty = new CSmartyDP;

$smarty->assign("affectations", $affectations);
$smarty->assign("readonly"  , $readonly);
$smarty->assign("_lit"      , $lit);
$smarty->assign("date"      , $date);
$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);

if ($prestation_id) {
  $smarty->assign("nb_ticks"  , $prestation_id ? $nb_ticks + 2 : $nb_ticks + 1);
}

$smarty->assign("nb_ticks_r", $nb_ticks-1);
$smarty->assign("datetimes" , $datetimes);
$smarty->assign("current"   , $current);
$smarty->assign("mode_vue_tempo", $mode_vue_tempo);
$smarty->assign("prestation_id", $prestation_id);
$smarty->assign("show_age_patient", CAppUI::conf("dPhospi show_age_patient"));
$smarty->assign("suivi_affectation", $suivi_affectation);
$smarty->assign("td_width"  , 84.2 / $nb_ticks);

$smarty->display("inc_line_lit.tpl");

?>