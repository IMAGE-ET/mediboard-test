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

foreach ($patient->_specs["status"]->_list as $_state) {
  $patient->status = $_state;
  $patients_count[CMbString::lower($_state)] = $patient->countMatchingList();
}

$smarty = new CSmartyDP();
$smarty->assign("patients_count", $patients_count);
$smarty->display("patient_state/vw_patient_state.tpl");