<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$patient_id = CValue::post('patient_id');

$patient = new CPatient();
if (!$patient->load($patient_id)) {
  CAppUI::stepAjax("Chargement impossible du patient", UI_MSG_ERROR);
}

$patient->patient_link_id = "";
if ($msg = $patient->store()) {
  CAppUI::stepAjax("Association du patient impossible : $msg", UI_MSG_ERROR);
}

CAppUI::stepAjax("$patient->_view désassocié");

CApp::rip();

?>