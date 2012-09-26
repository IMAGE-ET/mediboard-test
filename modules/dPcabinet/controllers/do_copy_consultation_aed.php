<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$consultation_id = CValue::post("consultation_id");
$plageconsult_id = CValue::post("plageconsult_id");
$heure           = CValue::post("heure");

$consult = new CConsultation;
$consult->load($consultation_id);

$new_consult = new CConsultation;
$new_consult->plageconsult_id = $plageconsult_id;
$new_consult->heure = $heure;
$new_consult->chrono = CConsultation::PLANIFIE;
$new_consult->patient_id = $consult->patient_id;

$msg = $new_consult->store();

if ($msg) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CConsultation-msg-create"), UI_MSG_OK);
}

echo CAppUI::getMsg();

CApp::rip();
