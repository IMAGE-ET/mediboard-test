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

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

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

$lits = array($lit_id => $lit);

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
  "sejour.confirme" => "IS NULL",
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
CMbObject::massLoadBackRefs($patients, "dossier_medical");
CPatient::massCountPhotoIdentite($patients);

foreach ($affectations as $_affectation_imc) {
  /* @var CAffectation $_affectation_imc*/
  if (CAppUI::conf("dPhospi vue_temporelle show_imc_patient", "CService-".$_affectation_imc->service_id)) {
    $_affectation_imc->loadRefSejour()->loadRefPatient()->loadRefLatestConstantes(null, array("poids", "taille"));
  }
}

$operations = array();

$suivi_affectation = false;

loadVueTempo($affectations, $suivi_affectation, $lits, $operations, $date_min, $date_max, $period, $prestation_id);

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

$lits_ids = $chambre->loadBackIds("lits");

foreach ($lits_ids as $_lit_id) {
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
