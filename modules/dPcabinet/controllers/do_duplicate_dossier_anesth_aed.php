<?php 

/**
 * Duplication de dossier d'anesthésie
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

$dossier_anesth_id = CValue::post("_consult_anesth_id");
$sejour_id         = CValue::post("sejour_id");
$operation_id      = CValue::post("operation_id");
$redirect          = CValue::post("redirect", 1);

$consult_anesth = new CConsultAnesth();
$consult_anesth->load($dossier_anesth_id);

$consult_anesth->_id = $consult_anesth->operation_id = $consult_anesth->sejour_id = null;

if ($sejour_id) {
  $consult_anesth->sejour_id = $sejour_id;
}

if ($operation_id) {
  $consult_anesth->operation_id = $operation_id;
}

$msg = $consult_anesth->store();

if ($msg) {
  CAppUI::setMsg($msg);
}
else {
  CAppUI::setMsg(CAppUI::tr("CConsultAnesth-msg-duplicate"));
}

echo CAppUI::getMsg();

if ($redirect) {
  $vw_tab = CAppUI::pref("new_consultation") ? "vw_consultation" : "edit_consultation";
  CAppUI::redirect(
    "m=cabinet&tab=$vw_tab&selConsult=".
    $consult_anesth->consultation_id."&dossier_anesth_id=".$consult_anesth->_id
  );
}

CApp::rip();