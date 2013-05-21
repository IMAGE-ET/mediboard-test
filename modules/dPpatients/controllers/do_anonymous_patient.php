<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$callback = CValue::post("callback");

$patient = new CPatient;
$patient->nom = "anonyme";
$patient->prenom = "anonyme";
$patient->sexe = CAppUI::conf("dPplanningOp CSejour anonymous_sexe");
$patient->naissance = CAppUI::conf("dPplanningOp CSejour anonymous_naissance");

$msg = $patient->store();

if ($msg) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CPatient-msg-create"), UI_MSG_OK);
}

echo CAppUI::getMsg();

if ($callback) {
  CAppUI::callbackAjax($callback, $patient->_id, $patient->_view);
}

CApp::rip();
