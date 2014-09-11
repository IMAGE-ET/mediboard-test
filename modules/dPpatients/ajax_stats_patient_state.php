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

CCanDo::checkAdmin();


$patient = new CPatient();
$count_patient = $patient->countList();
$count_status  = $patient->countMultipleList(array("status" => "IS NOT NULL"), null, "status", null, array("status"));

$series = array(
  "title"   => "CPatientState.proportion",
  "count"   => null,
  "unit"    => CAppUI::tr("CPatient"),
  "datum"   => array(),
  "options" => null
);

$total = 0;
foreach ($count_status as $_count) {
  $count  = $_count["total"];
  $status = $_count["status"];
  $total += $count;
  $series["datum"][] = array(
    "label"   => utf8_encode(CAppUI::tr("CPatient.status.$status")),
    "data" => $count
  );
}

$series["count"] = $total;
$series["options"] = array(
  "series" => array(
    "pie" => array(
      "innerRadius" => 0.5,
      "show" => true,
      "unit"    => CAppUI::tr("CPatient"),
      "label" => array(
        "show" => true,
        "threshold" => 0.02
      )
    )
  ),
  "legend" => array(
    "show" => false
  ),
  "colors" => array(
    "#33B1FF", "#CC9900", "#9999CC", "#FF66FF", "#DFDFE0", "#66CCFF", "#FF6666", "#009900",
    "#0066CC", "#996600", "#787878", "#66FF33", "#FF3300", "#00FF99", "#6666FF"
  ),
  "grid" => array(
    "hoverable" => true
  )
);

foreach ($series["datum"] as $_k => $_serie) {
  $series["datum"][$_k]["percent"] = round($_serie["data"] / $series["count"] * 100);
}

$smarty = new CSmartyDP();
$smarty->assign("graph"              , $series);
$smarty->assign("total_patient_state", $total);
$smarty->assign("total_patient"      , $count_patient);
$smarty->display("patient_state/inc_stats_patient_state.tpl");