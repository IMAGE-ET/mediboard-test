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
if (!CAppUI::pref("allowed_identity_status")) {
  //todo voir pour redirect
  CApp::rip();
}

$patients_count = array();
$patient        = new CPatient();

$date_min   = CValue::getOrSession("_date_min");
$date_max   = CValue::getOrSession("_date_max");
$where    = array();
$leftjoin = null;

CValue::setSession("patient_state_date_min", $date_min);
CValue::setSession("patient_state_date_max", $date_max);

if ($date_min) {
  $where["entree"] = ">= '$date_min'";
  $leftjoin["sejour"] = "patients.patient_id = sejour.patient_id";
}

if ($date_max) {
  $where["entree"] = "<= '$date_max'";
  $leftjoin["sejour"] = "patients.patient_id = sejour.patient_id";
}

foreach ($patient->_specs["status"]->_list as $_state) {
  $where["status"] = " = '$_state'";
  $patients_count[CMbString::lower($_state)] = (int)CPatientState::getNumberPatient($where, $leftjoin);
}

$smarty = new CSmartyDP();
$smarty->assign("patients_count", $patients_count);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->display("patient_state/inc_manage_patient_state.tpl");