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

CValue::setSession("_number_day", $number_day);
CValue::setSession("_date_end", $now);

$patient = new CPatient();
$count_patient = $patient->countList();
$count_status  = $patient->countMultipleList(array("status" => "IS NOT NULL"), null, "status", null, array("status"));

//répartition total
$series = CPatientStateTools::createGraphPie($count_status);
foreach ($series["datum"] as $_k => $_serie) {
  $series["datum"][$_k]["percent"] = round($_serie["data"] / $series["count"] * 100);
}

//Répartition par journée
$results = CPatientStateTools::getPatientStateByDate($before, $now);

$values = array();
foreach ($patient->_specs["status"]->_list as $_status) {
  for ($i=$number_day; $i>=0; $i--) {
    $values[$_status][CMbDT::date("-$i DAY", $now)] = 0;
  }
}

foreach ($results as $_result) {
  $values[$_result["state"]][$_result["date"]] = $_result["total"];
}

$series2 = CPatientStateTools::createGraphBar($values, $number_day);

$smarty = new CSmartyDP();
$smarty->assign("graph"        , $series);
$smarty->assign("graph2"       , $series2);
$smarty->assign("total_patient", $count_patient);
$smarty->assign("_number_day"  , $number_day);
$smarty->assign("_date_end"  , $now);
$smarty->display("patient_state/inc_stats_patient_state.tpl");