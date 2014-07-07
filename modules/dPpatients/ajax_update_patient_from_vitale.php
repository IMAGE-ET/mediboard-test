<?php 

/**
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$patient_id = CValue::get("patient_id");
$administrative_data = CValue::get('administrative_data', 0);
$patient = new CPatient();
$patient->load($patient_id);

$cv = CFseFactory::createCV();

if ($cv) {
  if ($patient->_id) {
    $cv->getPropertiesFromVitale($patient, $administrative_data);
  }
  else {
    $cv->getPropertiesFromVitale($patient);
  }

  $msg = $patient->store();

  if ($msg) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}