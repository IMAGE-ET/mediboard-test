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

$date_min   = CValue::getOrSession("_date_min");
$date_max   = CValue::getOrSession("_date_max");

CValue::setSession("patient_state_date_min", $date_min);
CValue::setSession("patient_state_date_max", $date_max);

$patients_count = CPatientState::getAllNumberPatient($date_min, $date_max);

$smarty = new CSmartyDP();
$smarty->assign("patients_count", $patients_count);
$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->display("patient_state/inc_manage_patient_state.tpl");