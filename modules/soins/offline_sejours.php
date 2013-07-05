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
CApp::setTimeLimit(120);

$service_id = CValue::get("service_id");
$date       = CValue::get("date", CMbDT::date());

$service = new CService();
$service->load($service_id);

$datetime_min = "$date 00:00:00";
$datetime_max = "$date 23:59:59";
$datetime_avg = "$date ".CMbDT::time();

$sejour = new CSejour();
$where  = array();
$ljoin  = array();

$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";

$order_by = "chambre.nom";

$where["sejour.entree"] = "<= '$datetime_max'";
$where["sejour.sortie"] = " >= '$datetime_min'";
$where["affectation.entree"] = "<= '$datetime_max'";
$where["affectation.sortie"] = ">= '$datetime_min'";
$where["service.service_id"] = " = '$service_id'";

$sejours = $sejour->loadList($where, $order_by, null, null, $ljoin);

CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($sejours, "praticien_id");
$dossiers_complets = array();

foreach ($sejours as $sejour) {
  $patient = $sejour->loadRefPatient();
  $patient->loadIPP();
  $sejour->loadRefPraticien();
  $sejour->checkDaysRelative($date);
  $sejour->loadNDA();
  $sejour->loadRefsNotes();
  $sejour->loadRefCurrAffectation($datetime_avg, $service_id)->loadRefLit();

  $params = array(
    "sejour_id" => $sejour->_id,
    "dialog" => 1,
    "offline" => 1,
    "in_modal" => 1
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