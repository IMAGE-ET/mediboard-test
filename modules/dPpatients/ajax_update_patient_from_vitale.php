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
$patient = new CPatient();
$patient->load($patient_id);

$cv = CFseFactory::createCV();

if ($cv) {
  $cv->getPropertiesFromVitale($patient);

  if ($msg = $patient->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}