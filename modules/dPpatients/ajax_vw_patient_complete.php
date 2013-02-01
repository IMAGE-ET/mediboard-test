<?php

/**
 * view of the complete patient data (only)
 *
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$pat_id = CValue::get("patient_id");

$patient = new CPatient();
$patient->load($pat_id);
$patient->loadComplete();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("object", $patient);
$smarty->display("CPatient_complete.tpl");