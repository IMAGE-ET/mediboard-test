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

$consult_anesth_id = CValue::post("consult_anesth_id");

$consult_anesth = new CConsultAnesth();
$consult_anesth->load($consult_anesth_id);

$consult_anesth->_id = $consult_anesth->sejour_id = $consult_anesth->operation_id = null;

if ($msg = $consult_anesth->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CConsultAnesth-msg-create"), UI_MSG_OK);
}

CAppUI::redirect($_POST["postRedirect"]);
