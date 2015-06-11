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

$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filter_function = CValue::getOrSession("filter_function");
$date            = CValue::getOrSession("date");
$granularite     = CValue::getOrSession("granularite");
$readonly        = CValue::getOrSession("readonly", 0);
$duree_uscpo     = CValue::getOrSession("duree_uscpo", "0");
$isolement       = CValue::getOrSession("isolement", "0");
$prestation_id   = CValue::getOrSession("prestation_id", "");
$item_prestation_id = CValue::getOrSession("item_prestation_id");

$group_id = CGroups::loadCurrent()->_id;

if (CAppUI::conf("dPhospi prestations systeme_prestations", $group_id) == "standard") {
  CValue::setSession("prestation_id", "");
  $prestation_id = "";
}

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$where = array();
$where["annule"] = "= '0'";
$where["sejour.group_id"] = "= '$group_id'";
$where[] = "(sejour.type != 'seances' && affectation.affectation_id IS NULL) || sejour.type = 'seances'";
$where["sejour.service_id"] = "IS NULL " . (is_array($services_ids) && count($services_ids) ?
    "OR `sejour`.`service_id` " . CSQLDataSource::prepareIn($services_ids) : "");

$order = null;
switch ($triAdm) {
  case "date_entree":
    $order = "entree ASC, sortie ASC";
    break;

  case "praticien":
    $order = "users_mediboard.function_id, users.user_last_name, users.user_first_name";
    break;

  case "patient":
    $order = "patients.nom, patients.prenom";
    break;

  default:
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
}

switch ($_type_admission) {
  case "ambucomp":
    $where["sejour.type"] = "IN ('ambu', 'comp', 'ssr')";
    break;

  case "0":
    break;

  default:
    $where["sejour.type"] = "= '$_type_admission'";
}

$sejour = new CSejour;
$ljoin = array(
  "affectation"     => "sejour.sejour_id = affectation.sejour_id",
  "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  "users"           => "users_mediboard.user_id = users.user_id",
  "patients"        => "sejour.patient_id = patients.patient_id"
);

$period = "";
$nb_unite = 0;

switch ($granularite) {
  case "day":
    $service_id = count($services_ids) == 1 ? reset($services_ids) : "";

    $hour_debut = 0;
    $hour_fin   = 23;

    if ($service_id) {
      $hour_debut = CAppUI::conf("dPhospi vue_temporelle hour_debut_day", "CService-$service_id");
      $hour_fin   = CAppUI::conf("dPhospi vue_temporelle hour_fin_day"  , "CService-$service_id");
    }

    // Inversion si l'heure de début est supérieure à celle de fin
    if ($hour_debut > $hour_fin) {
      list($hour_debut, $hour_fin) = array($hour_fin, $hour_debut);
    }

    $period = "1hour";
    $unite = "hour";
    $nb_unite = 1;
    $nb_ticks = $hour_fin - $hour_debut + 1;
    $date_min = "$date ".str_pad($hour_debut, 2, "0", STR_PAD_LEFT) . ":00:00";
    break;

  case "week":
    $period = "6hours";
    $unite = "hour";
    $nb_unite = 6;
    $nb_ticks = 28;
    $date_min = CMbDT::dateTime("-2 days", $date);
    break;

  case "4weeks":
    $period = "1day";
    $unite = "day";
    $nb_unite = 1;
    $nb_ticks = 28;
    $date_min = CMbDT::dateTime("-1 week", CMbDate::dirac("week", $date));

  default:
}

$offset = $nb_ticks * $nb_unite;
$date_max = CMbDT::dateTime("+ $offset $unite", $date_min);
$current = CMbDate::dirac("hour", CMbDT::dateTime());
$temp_datetime = CMbDT::dateTime(null, $date_min);

// Pour l'affichage des prestations en mode journée
if ($granularite == "day") {
  $date_max = CMbDT::dateTime("-1 second", $date_max);
}

for ($i = 0 ; $i < $nb_ticks ; $i++) {
  $offset = $i * $nb_unite;
  
  $datetime = CMbDT::dateTime("+ $offset $unite", $date_min);
  $datetimes[] = $datetime;
  if ($granularite == "4weeks") {
    if (CMbDT::date($current) == CMbDT::date($temp_datetime) && CMbDT::time($current) >= CMbDT::time($temp_datetime)
        && CMbDT::time($current) > CMbDT::time($datetime)) {
      $current = $temp_datetime;
    }
    $week_a = CMbDT::transform($temp_datetime, null, "%V");
    $week_b = CMbDT::transform($datetime, null, "%V");

    // les semaines
    $days[$datetime] = $week_b;
    
    // On stocke le changement de mois s'il advient
   if (CMbDT::transform($datetime, null, "%m") != CMbDT::transform($temp_datetime, null, "%m")) {
     // Entre deux semaines
     if ($i%7 == 0) {
       $change_month[$week_a] = array("right"=>$temp_datetime);
       $change_month[$week_b] = array("left"=>$datetime);
     }
     // Dans la même semaine
     else {
       $change_month[$week_b] = array("left" => $temp_datetime, "right" => $datetime);
     }
   }
  }
  else {
    if ($granularite == "week" && CMbDT::date($current) == CMbDT::date($temp_datetime) &&
        CMbDT::time($datetime) >= CMbDT::time($temp_datetime) && CMbDT::time($current) <= CMbDT::time($datetime))
    {
      $current = $temp_datetime;
    }
    // le datetime, pour avoir soit le jour soit l'heure
    $days[] = CMbDT::date($datetime);
  }
  $temp_datetime = $datetime;
}

$days = array_unique($days);

// Cas de la semaine 00
if ($granularite == "4weeks" && count($days) == 5) {
  array_pop($days);
}

$where["sejour.entree"] = "< '$date_max'";
$where["sejour.sortie"] = "> '$date_min'";

if ($duree_uscpo) {
  $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
  $where["duree_uscpo"] = "> 0";
}

if ($isolement) {
  $where["isolement"] = "= '1'";
}

$items_prestation = array();
if ($prestation_id) {
  $prestation = new CPrestationJournaliere;
  $prestation->load($prestation_id);
  $items_prestation = $prestation->loadBackRefs("items", "rank asc");
}

if ($item_prestation_id && $prestation_id) {
  // L'axe de prestation a pu changer, donc ne pas appliquer l'item de prestation s'il ne fait pas partie de l'axe choisi
  if (isset($items_prestation[$item_prestation_id])) {
    $ljoin["item_liaison"]                 = "sejour.sejour_id = item_liaison.sejour_id";
    $where["item_liaison.item_souhait_id"] = " = '$item_prestation_id'";
  }
}

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$praticiens = CStoredObject::massLoadFwdRef($sejours, "praticien_id");
CStoredObject::massLoadFwdRef($sejours, "prestation_id");
CStoredObject::massLoadFwdRef($praticiens, "function_id");
CStoredObject::massLoadFwdRef($sejours, "patient_id");
$services = CStoredObject::massLoadFwdRef($sejours, "service_id");

foreach ($sejours as $_sejour_imc) {
  /* @var CAffectation $_affectation_imc*/
  $conf_imc = $_sejour_imc->service_id ? "CService-".$_sejour_imc->service_id : CGroups::loadCurrent();
  if (CAppUI::conf("dPhospi vue_temporelle show_imc_patient", $conf_imc)) {
    $_sejour_imc->loadRefPatient()->loadRefLatestConstantes(null, array("poids", "taille"));
  }
}

$sejours_non_affectes = array();
$functions_filter = array();
$operations = array();
$suivi_affectation = false;

// Chargement des affectations dans les couloirs (sans lit_id)
$where = array();

$ljoin = array(
  "sejour"          => "sejour.sejour_id = affectation.sejour_id",
  "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  "users"           => "users_mediboard.user_id = users.user_id",
  "patients"        => "sejour.patient_id = patients.patient_id"
);

$where["lit_id"] = "IS NULL";
if (is_array($services_ids) && count($services_ids)) {
  $where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
}
$where["affectation.entree"] = "<= '$date_max'";
$where["affectation.sortie"] = ">= '$date_min'";

$where["sejour.annule"] = "= '0'";

if ($duree_uscpo) {
  $ljoin["operations"] = "operations.sejour_id = affectation.sejour_id";
  $where["duree_uscpo"] = "> 0";
}

if ($isolement) {
  $ljoin["sejour"] = "sejour.sejour_id = affectation.sejour_id";
  $where["isolement"] = "= '1'";
}

if ($item_prestation_id && $prestation_id) {
  if (isset($items_prestation[$item_prestation_id])) {
    $ljoin["item_liaison"]                 = "affectation.sejour_id = item_liaison.sejour_id";
    $where["item_liaison.item_souhait_id"] = " = '$item_prestation_id'";
  }
}

$affectation = new CAffectation();

$affectations = $affectation->loadList($where, $order, null, null, $ljoin);
$_sejours  = CStoredObject::massLoadFwdRef($affectations, "sejour_id");
$services = $services + CStoredObject::massLoadFwdRef($affectations, "service_id");
$patients = CStoredObject::massLoadFwdRef($_sejours, "patient_id");
CPatient::massCountPhotoIdentite($patients);

foreach ($affectations as $_affectation_imc) {
  /* @var CAffectation $_affectation_imc*/
  if (CAppUI::conf("dPhospi vue_temporelle show_imc_patient", "CService-".$_affectation_imc->service_id)) {
    $_affectation_imc->loadRefSejour()->loadRefPatient()->loadRefLatestConstantes(null, array("poids", "taille"));
  }
}

// Préchargement des users
$user = new CUser();
$where = array("user_id" => CSQLDataSource::prepareIn(CMbArray::pluck($_sejours, "praticien_id")));
$users = $user->loadList($where);

$praticiens = CStoredObject::massLoadFwdRef($_sejours, "praticien_id");
CStoredObject::massLoadFwdRef($praticiens, "function_id");
CStoredObject::massCountBackRefs($affectations, "affectations_enfant");
$_operations = CStoredObject::massLoadBackRefs($sejours, "operations", "date ASC");
CStoredObject::massLoadFwdRef($_operations, "plageop_id");

loadVueTempo(
  $sejours, $suivi_affectation, null, $operations, $date_min, $date_max, $period, $prestation_id, $functions_filter,
  $filter_function, $sejours_non_affectes
);
if (CAppUI::conf("dPadmissions show_deficience")) {
  CStoredObject::massLoadBackRefs($patients, "dossier_medical");
  $dossiers = CMbArray::pluck($sejours, "_ref_patient", "_ref_dossier_medical");
  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");
}

loadVueTempo(
  $affectations, $suivi_affectation, null, $operations, $date_min, $date_max, $period, $prestation_id, $functions_filter,
  $filter_function, $sejours_non_affectes
);

if (count($affectations) && CAppUI::conf("dPadmissions show_deficience")) {
  $dossiers = CMbArray::pluck($affectations, "_ref_sejour", "_ref_patient", "_ref_dossier_medical");
  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");
}
ksort($sejours_non_affectes, SORT_STRING);

$_sejour = new CSejour();
$_sejour->_type_admission = $_type_admission;

$smarty = new CSmartyDP();

$smarty->assign("sejours_non_affectes", $sejours_non_affectes);
$smarty->assign("_sejour"             , $_sejour);
$smarty->assign("triAdm"              , $triAdm);
$smarty->assign("functions_filter"    , $functions_filter);
$smarty->assign("filter_function"     , $filter_function);
$smarty->assign("granularite"         , $granularite);
$smarty->assign("date"                , $date);
$smarty->assign("date_min"            , $date_min);
$smarty->assign("date_max"            , $date_max);
$smarty->assign("nb_ticks"            , $nb_ticks);
$smarty->assign("days"                , $days);
$smarty->assign("datetimes"           , $datetimes);
$smarty->assign("readonly"            , $readonly);
$smarty->assign("duree_uscpo"         , $duree_uscpo);
$smarty->assign("isolement"           , $isolement);
$smarty->assign("current"             , $current);
$smarty->assign("items_prestation"    , $items_prestation);
$smarty->assign("item_prestation_id"  , $item_prestation_id);
$smarty->assign("prestation_id"       , $prestation_id);
$smarty->assign("td_width"            , CAffectation::$width_vue_tempo / $nb_ticks);
$smarty->assign("mode_vue_tempo"      , "classique");
$smarty->assign("affectations"        , $affectations);
$smarty->assign("sejours"             , $sejours);
$smarty->assign("services"            , $services);

$smarty->display("inc_vw_non_places.tpl");
