<?php 

/**
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$service_id = CValue::get("service_id");

// Le service_id en get lors du fetch pour le plan de soins pose problème
unset($_GET["service_id"]);

$date       = CValue::get("date", CMbDT::date());

$service = new CService();
$service->load($service_id);

$datetime_min = "$date 00:00:00";
$datetime_max = "$date 23:59:59";
$datetime_avg = "$date ".CMbDT::time();

$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = CMbDT::date("-$date_tolerance DAY", $date);
$date_after  = CMbDT::date("+1 DAY", $date);

$group = CGroups::loadCurrent();

$sejour = new CSejour();
$where  = array();
$ljoin  = array();

$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$where["sejour.entree"] = "<= '$datetime_max'";
$where["sejour.sortie"] = " >= '$datetime_min'";
$where["sejour.group_id"] = "= '$group->_id'";

switch ($service_id) {
  case "NP":
    $where["affectation.affectation_id"] = "IS NULL";
    $where["sejour.group_id"] = "= '$group->_id'";
    break;
  case "urgence":
    $ljoin["rpu"]      = "sejour.sejour_id = rpu.sejour_id";
    $ljoin["patients"] = "sejour.patient_id = patients.patient_id";
    $where[] = "sejour.entree BETWEEN '$date' AND '$date_after'
                OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after' AND sejour.annule = '0')";
    $where[] = CAppUI::pref("showMissingRPU") ?
      "sejour.type = 'urg' OR rpu.rpu_id IS NOT NULL" :
      "rpu.rpu_id IS NOT NULL";
    $where["sejour.sortie_reelle"] = "IS NULL";
    $where["sejour.annule"] = " = '0'";
    if (CAppUI::conf("dPurgences create_sejour_hospit")) {
      $where["rpu.mutation_sejour_id"] = "IS NULL";
    }
    break;
  default:
    $where["affectation.entree"]     = "<= '$datetime_max'";
    $where["affectation.sortie"]     = ">= '$datetime_min'";
    $where["affectation.service_id"] = " = '$service_id'";
}

$patients_offline = array();

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);

CSejour::massLoadCurrAffectation($sejours, $datetime_avg, $service_id);
CSejour::massLoadNDA($sejours);
/** @var CPatient[] $patients */
$patients = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CPatient::massLoadIPP($patients);
CStoredObject::massCountBackRefs($sejours, "operations");

// Recherche de transmissions // observations // consultations
$sejours_ids = CMbArray::pluck($sejours, "_id");

$where = array("sejour_id" => CSQLDataSource::prepareIn($sejours_ids));
$ljoin = array("users_mediboard" => "users_mediboard.user_id = transmission_medicale.user_id");
$group_by = "users_mediboard.function_id";
$transmission = new CTransmissionMedicale();
$transmissions = $transmission->loadList($where, "date DESC", null, $group_by, $ljoin);
CStoredObject::massLoadFwdRef($transmissions, "user_id");

$ljoin = array("users_mediboard" => "users_mediboard.user_id = observation_medicale.user_id");
$observation  = new CObservationMedicale();
$observations = $observation->loadList($where, "date DESC", null, $group_by, $ljoin);
CStoredObject::massLoadFwdRef($observations, "user_id");

$ljoin = array(
  "plageconsult"    => "plageconsult.plageconsult_id = consultation.plageconsult_id",
  "users_mediboard" => "users_mediboard.user_id = plageconsult.chir_id"
);
$consultation = new CConsultation();
$consultations = $consultation->loadList($where, "plageconsult.date", null, $group_by, $ljoin);
CStoredObject::massLoadFwdRef($consultations, "plageconsult_id");

$smarty_cstes = new CSmartyDP("modules/dPpatients");
$smarty_cstes->assign("empty_lines", 2);
$smarty_cstes->assign("offline", 1);

$delay_trans_obs_consult = 3;

// Constantes des 12 dernières heures
$where_cste = array("datetime" => "BETWEEN '" . CMbDT::subDateTime("12:00:00", $datetime_avg) . "' AND '$datetime_avg'");

CPrescriptionLine::$_offline_lite = true;
CPrescriptionLineMix::$_offline_lite = true;
CPrescription::$_offline_lite = true;

foreach ($sejours as $_sejour) {
  $patient = $_sejour->loadRefPatient();

  $_sejour->loadRefPraticien();
  $_sejour->loadJourOp($date);

  if ($service_id == "urgence") {
    $_sejour->_veille = CMbDT::date($_sejour->entree) != $date;
    $_sejour->loadRefRPU()->loadRefIDEResponsable();
  }
  $patients_offline[$patient->_guid]["sejour"] = $_sejour;

  // Plan de soins
  $params = array(
    "sejours_ids"  => $_sejour->_id,
    "date"         => $date,
    "hours_before" => "2",
    "hours_after"  => "2",
    "empty_lines"  => "2",
    "dialog"       => 1,
    "mode_lite"    => 1
  );

  $patients_offline[$patient->_guid]["plan_soins"] = CApp::fetch("soins", "offline_plan_soins", $params);


  // Transmissions
  $patients_offline[$patient->_guid]["transmissions"] = array();

  foreach ($transmissions as $_trans) {
    if ($_trans->sejour_id != $_sejour->_id || $_trans->locked || CMbDT::daysRelative($_trans->date, $date) > $delay_trans_obs_consult) {
      continue;
    }
    $_trans->loadTargetObject();
    $_trans->calculCibles();
    $_trans->loadRefUser();
    $patients_offline[$patient->_guid]["transmissions"][$_trans->_ref_user->function_id] = $_trans;
  }


  // Observations
  $_sejour->loadRefObsEntree()->loadRefPraticien()->loadRefFunction();

  $patients_offline[$patient->_guid]["observations"] = array();

  foreach ($observations as $_observation) {
    if ($_observation->sejour_id != $_sejour->_id || CMbDT::daysRelative($_observation->date, $date) > $delay_trans_obs_consult) {
      continue;
    }
    $_observation->loadRefUser()->loadRefFunction();
    $_observation->loadTargetObject();
    $patients_offline[$patient->_guid]["observations"][$_observation->_ref_user->function_id] = $_observation;
  }


  // Consultations
  $patients_offline[$patient->_guid]["consultations"] = array();

  foreach ($consultations as $_consultation) {
    if ($_consultation->sejour_id != $_sejour->_id || $_consultation->type == "entree") {
      continue;
    }
    $_consultation->loadRefPraticien()->loadRefFunction();
    if (CMbDT::daysRelative($_consultation->_datetime, $date) > $delay_trans_obs_consult) {
      continue;
    }

    $patients_offline[$patient->_guid]["consultations"][$_consultation->_ref_chir->function_id] = $_consultation;
  }


  // Constantes
  $patients_offline[$patient->_guid]["constantes"] = "";
  $cstes = array_reverse($_sejour->loadListConstantesMedicales($where_cste));
  if (count($cstes)) {
    CStoredObject::massLoadFwdRef($cstes, "user_id");
    foreach ($cstes as $_cste) {
      $_cste->loadRefUser();
    }
    $smarty_cstes->assign("constantes_medicales_grid", CConstantesMedicales::buildGrid($cstes, false));
    $smarty_cstes->assign("sejour", $_sejour);
    $patients_offline[$patient->_guid]["constantes"] = $smarty_cstes->fetch("print_constantes.tpl", '', '', 0);
  }
}

if ($service_id != "urgence") {
  array_multisort(CMbArray::pluck($patients_offline, "sejour", "_ref_patient", "nom"), SORT_ASC, $patients_offline);
}

$smarty = new CSmartyDP();

$smarty->assign("date"            , $date);
$smarty->assign("service"         , $service);
$smarty->assign("service_id"      , $service_id);
$smarty->assign("patients_offline", $patients_offline);

$smarty->display("offline_sejours_lite.tpl");