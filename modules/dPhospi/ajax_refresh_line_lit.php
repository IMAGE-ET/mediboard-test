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
$group_id = CGroups::loadCurrent()->_id;

switch ($granularite) {
  case "day":
    $unite = "hour";
    $nb_unite = 1;
    $nb_ticks = 24;
    $step = "+1 hour";
    $period = "1hour";
    $date_min = CMbDT::dateTime($date);
    $date_before = CMbDT::date("-1 day", $date);
    $date_after  = CMbDT::date("+1 day", $date);
    break;
  case "week":
    $unite = "hour";
    $nb_unite = 6;
    $nb_ticks = 28;
    $step = "+6 hours";
    $period = "6hours";
    $date_min = CMbDT::dateTime("-2 days", $date);
    $date_before = CMbDT::date("-1 week", $date);
    $date_after = CMbDT::date("+1 week", $date);
    break;
  case "4weeks":
    $unite = "day";
    $nb_unite = 1;
    $nb_ticks = 28;
    $step = "+1 day";
    $period = "1day";
    $date_min = CMbDT::dateTime("-1 week", CMbDate::dirac("week", $date));
    $date_before = CMbDT::date("-4 week", $date);
    $date_after = CMbDT::date("+4 week", $date);
}

$current = CMbDate::dirac("hour", CMbDT::dateTime());
$offset = $nb_ticks * $nb_unite;

$date_max = CMbDT::dateTime("+ $offset $unite", $date_min);
$temp_datetime = CMbDT::dateTime(null, $date_min);

for ($i = 0 ; $i < $nb_ticks ; $i++) {
  $offset = $i * $nb_unite;
  
  $datetime = CMbDT::dateTime("+ $offset $unite", $date_min);
  $datetimes[] = $datetime;
}

$lit = new CLit;
$lit->load($lit_id);
$lit->_ref_affectations = array();
$chambre = $lit->loadRefChambre();
$chambre->_ref_lits[$lit->_id] = $lit;

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
$affectations = $affectation->loadList($where, "parent_affectation_id ASC");

// Ajout des prolongations anormales
// (séjours avec entrée réelle et sortie non confirmée et sortie < maintenant
$nb_days_prolongation = CAppUI::conf("dPhospi nb_days_prolongation");
$sejour = new CSejour();
$max = CMbDT::dateTime();
$min = CMbDT::date("-$nb_days_prolongation days", $max) . " 00:00:00";
$where = array(
  "entree_reelle"   => "IS NOT NULL",
  "sortie_reelle"   => "IS NULL",
  "sortie_prevue"   => "BETWEEN '$min' AND '$max'",
  "sejour.confirme" => "= '0'",
  "group_id"        => "= '$group_id'"
);

$sejours_prolonges = $sejour->loadList($where);

$affectations_prolong = array();
foreach ($sejours_prolonges as $_sejour) {
  $aff = $_sejour->getCurrAffectation($_sejour->sortie);
  if (!$aff->_id || $aff->lit_id != $lit_id) {
    continue;
  }

  $aff->_is_prolong = true;
  $affectations[$aff->_id] = $aff;
}

$sejours  = CMbObject::massLoadFwdRef($affectations, "sejour_id");
$patients = CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadFwdRef($praticiens, "function_id");
$operations = array();

$suivi_affectation = false;

/** @var $affectations CAffectation[] */
foreach ($affectations as $_affectation) {
  if (!$suivi_affectation && $_affectation->parent_affectation_id) {
    $suivi_affectation = true;
  }
  $_affectation->_entree = $_affectation->entree;
  $_affectation->_sortie = $_affectation->sortie;
  if ($_affectation->_is_prolong) {
    $_affectation->_sortie = CMbDT::dateTime();
  }
  $_affectation->loadRefsAffectations();
  $sejour = $_affectation->loadRefSejour();
  $sejour->loadRefPraticien()->loadRefFunction();
  $_affectation->_ref_sejour->loadRefChargePriceIndicator();
  $patient = $sejour->loadRefPatient();
  $patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical(false)->loadRefsAntecedentsOfType("deficience");
  $constantes = $patient->getFirstConstantes();
  $patient->_overweight = $constantes->poids > 120;
  
  $lit->_ref_affectations[$_affectation->_id] = $_affectation;
  $_affectation->_entree_offset = CMbDate::position(max($date_min, $_affectation->_entree), $date_min, $period);
  $_affectation->_sortie_offset = CMbDate::position(min($date_max, $_affectation->_sortie), $date_min, $period);
  $_affectation->_width = $_affectation->_sortie_offset - $_affectation->_entree_offset;
  
  if (isset($operations[$sejour->_id])) {
    $_operations = $operations[$sejour->_id];
  }
  else {
    $operations[$sejour->_id] = $_operations = $sejour->loadRefsOperations();
  }
  
  foreach ($_operations as $key=>$_operation) {
    $_operation->loadRefPlageOp(1);
    
    $hour_operation = CMbDT::format($_operation->temp_operation, "%H");
    $min_operation = CMbDT::format($_operation->temp_operation, "%M");
    
    $_operation->_debut_offset[$_affectation->_id] = CMbDate::position($_operation->_datetime, max($date_min, $_affectation->_entree), $period);
    
    $_operation->_fin_offset[$_affectation->_id] = CMbDate::position(CMbDT::dateTime("+$hour_operation hours +$min_operation minutes",$_operation->_datetime), max($date_min, $_affectation->_entree), $period);
    $_operation->_width[$_affectation->_id] = $_operation->_fin_offset[$_affectation->_id] - $_operation->_debut_offset[$_affectation->_id];
    
    if (($_operation->_datetime > $date_max)) {
      $_operation->_width_uscpo[$_affectation->_id] = 0;
    }
    else {
      $fin_uscpo = $hour_operation + 24 * $_operation->duree_uscpo;
      $_operation->_width_uscpo[$_affectation->_id] = CMbDate::position(CMbDT::dateTime("+$fin_uscpo hours + $min_operation minutes", $_operation->_datetime), max($date_min, $_affectation->_entree), $period) - $_operation->_fin_offset[$_affectation->_id];
    }

    if ($_affectation->_is_prolong) {
      $_affectation->_start_prolongation = CMbDate::position(max($date_min, $_affectation->_entree), $date_min, $period);
      $_affectation->_end_prolongation   = CMbDate::position(min($date_max, $_affectation->_sortie), $date_min, $period);
      $_affectation->_width_prolongation = $_affectation->_end_prolongation - $_affectation->_start_prolongation;
    }
  }
  
  if ($prestation_id) {
    $sejour->loadLiaisonsForPrestation($prestation_id);
  }
}

$intervals = array();
if (count($lit->_ref_affectations)) {
  foreach ($lit->_ref_affectations as $_affectation) {
    $intervals[$_affectation->_id] = array(
      "lower" => $_affectation->_entree,
      "upper" => $_affectation->_sortie,
    );
  }
  $lit->_lines = CMbRange::rearrange($intervals);
}

// Pour les alertes, il est nécessaire de charger les autres lits
// de la chambre concernée ainsi que les affectations

$where = array();
$where["entree"] = "<= '$date_max'";
$where["sortie"] = ">= '$date_min'";

$lits = $chambre->loadBackIds("lits");

foreach ($lits as $_lit_id) {
  if ($lit_id == $_lit_id) {
    continue;
  }
  $_lit = new CLit;
  $_lit->load($_lit_id);
  
  $where["lit_id"] = "= '$_lit->_id'";
  
  $_affectations = $affectation->loadList($where);
  
  $_sejours = CMbObject::massLoadFwdRef($_affectations, "sejour_id");
  CMbObject::massLoadFwdRef($_sejours, "patient_id");
  CMbObject::massLoadFwdRef($_sejours, "praticien_id");

  /** @var $_affectations CAffectation[] */
  foreach ($_affectations as $_affectation) {
    $_sejour = $_affectation->loadRefSejour();
    $_sejour->loadRefPraticien();
    $_sejour->loadRefPatient();
  }
  
  $_lit->_ref_affectations = $_affectations;
  
  $chambre->_ref_lits[$_lit->_id] = $_lit;
}

if (!CAppUI::conf("dPhospi hide_alertes_temporel")) {
  $lit->_ref_chambre->checkChambre();
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
