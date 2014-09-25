<?php 

/**
 * $Id$
 *  
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

if (!CAppUI::pref("allowed_modify_identity_status")) {
  CAppUI::redirect("m=system&a=access_denied");
}

$number_day = CValue::getOrSession("_number_day", 8);
$number_day = $number_day ?: 8;
$now        = CValue::getOrSession("_date_end", CMbDT::date());
$before     = CMbDT::date("-$number_day DAY", $now);

$csv = new CCSVFile();

$line = array(
  "Date",
  CAppUI::tr("CPatient.status.PROV"),
  CAppUI::tr("CPatient.status.DPOT"),
  CAppUI::tr("CPatient.status.ANOM"),
  CAppUI::tr("CPatient.status.CACH"),
  CAppUI::tr("CPatient.status.VALI"),
);
$csv->writeLine($line);

$results = CPatientStateTools::getPatientStateByDate($before, $now);


$values = array();
for ($i=$number_day; $i>=0; $i--) {
  $values[CMbDT::date("-$i DAY", $now)] = array(
    "PROV" => 0,
    "DPOT" => 0,
    "ANOM" => 0,
    "CACH" => 0,
    "VALI" => 0,
  );
}

foreach ($results as $_result) {
  $values[$_result["date"]][$_result["state"]] = $_result["total"];
}

foreach ($values as $_date => $_value) {
  $line = array(
    $_date
  );
  $line = array_merge($line, array_values($_value));

  $csv->writeLine($line);
}

$csv->stream("statut_patient_par_date");