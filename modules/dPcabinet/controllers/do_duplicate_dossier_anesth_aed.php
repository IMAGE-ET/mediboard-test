<?php 

/**
 * Duplication de dossier d'anesthésie
 *  
 * @category dPcabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

$dossier_anesth_id = CValue::post("_consult_anesth_id");

$consult_anesth = new CConsultAnesth();
$consult_anesth->load($dossier_anesth_id);

$consult_anesth->_id = null;

$msg = $consult_anesth->store();

if ($msg) {
  CAppUI::setMsg($msg);
}
else {
  CAppUI::setMsg(CAppUI::tr("CConsultAnesth-msg-duplicate"));
}

CAppUI::redirect("m=cabinet&tab=edit_consultation&selConsult=".$consult_anesth->consultation_id."&dossier_anesth_id=".$consult_anesth->_id);