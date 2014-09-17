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

$smarty = new CSmartyDP();
$smarty->display("patient_state/vw_patient_state.tpl");