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

$consult_id = CValue::post("consult_id");

$dossier_anesth = new CConsultAnesth();
$dossier_anesth->consultation_id = $consult_id;

if ($msg = $dossier_anesth->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CConsultAnesth-msg-create"), UI_MSG_OK);
}

CAppUI::redirect($_POST["postRedirect"]);
