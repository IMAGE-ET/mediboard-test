<?php

/**
 * Vue offline des séjours
 *
 * @category soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CApp::setMemoryLimit("1200M");
CApp::setTimeLimit(240);

$service_id = CValue::get("service_id");
$date       = CValue::get("date", CMbDT::date());
$embed      = CValue::get("embed");

CView::enableSlave();

$service = new CService();
$service->load($service_id);

$datetime_min = "$date 00:00:00";
$datetime_max = "$date 23:59:59";
$datetime_avg = "$date ".CMbDT::time();

$group = CGroups::loadCurrent();

$sejour = new CSejour();
$where  = array();
$ljoin  = array();

$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$where["sejour.entree"] = "<= '$datetime_max'";
$where["sejour.sortie"] = " >= '$datetime_min'";

if ($service_id == "NP") {
  $where["affectation.affectation_id"] = "IS NULL";
  $where["sejour.group_id"] = "= '$group->_id'";
}
else {
  $where["affectation.entree"]     = "<= '$datetime_max'";
  $where["affectation.sortie"]     = ">= '$datetime_min'";
  $where["affectation.service_id"] = " = '$service_id'";
}

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);

CSejour::massLoadCurrAffectation($sejours, $datetime_avg, $service_id);
CStoredObject::massLoadFwdRef($sejours, "praticien_id");
CMbObject::massLoadRefsNotes($sejours);
CSejour::massLoadNDA($sejours);
/** @var CPatient[] $patients */
$patients = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CPatient::massLoadIPP($patients);
foreach ($sejours as $sejour) {
  $patient = $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->checkDaysRelative($date);
  $sejour->loadRefsNotes();
}

$sorter_patient     = CMbArray::pluck($sejours, "_ref_patient", "nom");

if ($service_id == "NP") {
  array_multisort(
    $sorter_patient, SORT_ASC,
    $sejours
  );
}
else {
  $sorter_affectation = CMbArray::pluck($sejours, "_ref_curr_affectation", "_ref_lit", "_view");
  array_multisort(
    $sorter_affectation, SORT_ASC,
    $sorter_patient, SORT_ASC,
    $sejours
  );
}
$period = CAppUI::conf("soins offline_sejour period", $group);
$dossiers_complets = array();

foreach ($sejours as $sejour) {
  $params = array(
    "sejour_id" => $sejour->_id,
    "dialog"    => 1,
    "offline"   => 1,
    "in_modal"  => 1,
    "embed"     => $embed,
    "period"    => $period
  );

  $dossiers_complets[$sejour->_id] = CApp::fetch("soins", "print_dossier_soins", $params);
}

$smarty = new CSmartyDP();

$smarty->assign("date"   , $date);
$smarty->assign("hour"   , CMbDT::time());
$smarty->assign("service", $service);
$smarty->assign("sejours", $sejours);
$smarty->assign("dossiers_complets", $dossiers_complets);

$smarty->display("offline_sejours.tpl");