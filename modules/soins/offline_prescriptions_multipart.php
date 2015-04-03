<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

ob_clean();

CApp::setMemoryLimit("1024M");
CApp::setTimeLimit(240);

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

$where["sejour.entree"] = "<= '$datetime_max'";
$where["sejour.sortie"] = " >= '$datetime_min'";
$where["affectation.entree"] = "<= '$datetime_max'";
$where["affectation.sortie"] = ">= '$datetime_min'";
$where["affectation.service_id"] = " = '$service_id'";

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);

$ordonnances = array();

foreach ($sejours as $_sejour) {
  $_prescription = $_sejour->loadRefPrescriptionSejour();
  $_patient = $_sejour->loadRefPatient();

  $params = array(
    "prescription_id" => $_prescription->_id ? : "",
    "in_progress"     => 1,
    "multipart"       => 1
  );

  $_content = CApp::fetch("dPprescription", "print_prescription_fr", $params);
  $_naissance = str_replace("/", "-", $_patient->getFormattedValue("naissance"));
  $ordonnances[] = array(
    "title"     => base64_encode($_patient->_view . " - " . $_naissance),
    "content"   => base64_encode($_content),
    "extension" => "pdf",
  );
}

CApp::json($ordonnances);
