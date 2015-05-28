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

$merge_patient = CValue::getOrSession("_merge_patient");
$number_day    = CValue::getOrSession("_number_day", 8);

$number_day = $number_day ?: 8;
if ($number_day > 31) {
  $number_day = 31;
}
elseif ($number_day < 0) {
  $number_day = 0;
}

$now    = CValue::getOrSession("_date_end", CMbDT::date());
$before = CMbDT::date("-$number_day DAYS", $now);

CValue::setSession("_number_day", $number_day);
CValue::setSession("_date_end", $now);
CValue::setSession("_merge_patient", $merge_patient);

$patient       = new CPatient();
$count_patient = $patient->countList();
$count_status  = $patient->countMultipleList(array("status" => "IS NOT NULL"), null, "status", null, array("status"));

$patient_link   = new CPatientLink();
$count_status[] = array("total" => $patient_link->countList(), "status" => "DPOT");
//répartition total
$series = CPatientStateTools::createGraphPie($count_status);
foreach ($series["datum"] as $_k => $_serie) {
  $series["datum"][$_k]["percent"] = ($series["count"] > 0) ? round($_serie["data"] / $series["count"] * 100) : 0;
}

//Répartition par journée
$values = array();
$ids = array();
if ($merge_patient) {
  $results = CPatientStateTools::getPatientMergeByDate($before, $now);
  for ($i = $number_day; $i >= 0; $i--) {
    $values["merged"][CMbDT::date("-$i DAYS", $now)] = 0;
  }

  foreach ($results as $_result) {
    $values["merged"][$_result["date"]] = array('count' => $_result["total"], 'ids' => $_result['ids']);
  }
}
else {
  $results = CPatientStateTools::getPatientStateByDate($before, $now);

  foreach ($patient->_specs["status"]->_list as $_status) {
    for ($i = $number_day; $i >= 0; $i--) {
      $values[$_status][CMbDT::date("-$i DAYS", $now)] = 0;
    }
  }

  foreach ($results as $_result) {
    $values[$_result["state"]][$_result["date"]] = $_result["total"];
  }
}

$series2 = CPatientStateTools::createGraphBar($values, $number_day);

$smarty = new CSmartyDP();
$smarty->assign("graph", $series);
$smarty->assign("graph2", $series2);
$smarty->assign("total_patient", $count_patient);
$smarty->assign("_number_day", $number_day);
$smarty->assign("_date_end", $now);
$smarty->assign("_merge_patient", $merge_patient);
$smarty->display("patient_state/inc_stats_patient_state.tpl");