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

CApp::setMemoryLimit("1024M");
CApp::setTimeLimit(240);

$service_id = CValue::get("service_id");
$date       = CValue::get("date", CMbDT::date());
$embed      = CValue::get("embed");

$service = new CService();
$service->load($service_id);

$datetime_min = "$date 00:00:00";
$datetime_max = "$date 23:59:59";
$datetime_avg = "$date ".CMbDT::time();

$sejour = new CSejour();
$where  = array();
$ljoin  = array();

$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$where["sejour.entree"] = "<= '$datetime_max'";
$where["sejour.sortie"] = " >= '$datetime_min'";
$where["affectation.entree"] = "<= '$datetime_max'";
$where["affectation.sortie"] = ">= '$datetime_min'";
$where["affectation.service_id"] = " = '$service_id'";

$sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);

CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($sejours, "praticien_id");

$dossiers_complets = array();

/** @var CSejour[] $sejours */
foreach ($sejours as $sejour) {
  $patient = $sejour->loadRefPatient();
  $patient->loadIPP();
  $sejour->loadRefPraticien();
  $sejour->checkDaysRelative($date);
  $sejour->loadNDA();
  $sejour->loadRefsNotes();
  $sejour->loadRefCurrAffectation($datetime_avg, $service_id)->loadRefLit();
}

$sorter_affectation = CMbArray::pluck($sejours, "_ref_curr_affectation", "_ref_lit", "_view");
$sorter_patient     = CMbArray::pluck($sejours, "_ref_patient", "nom");

array_multisort(
  $sorter_affectation, SORT_ASC,
  $sorter_patient, SORT_ASC,
  $sejours
);

$period = CAppUI::conf("soins offline_sejour period", CGroups::loadCurrent()->_guid);

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