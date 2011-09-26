<?php 
/**
 * Receive message EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$sender_soap_id = CValue::get("sender_soap_id");
$message        = CValue::get("message");

$sender_soap = new CSenderSOAP();
$sender_soap->load($sender_soap_id);

$soap_handler = new CEAISoapHandler();
// Dispatch EAI 
if (!$ack = $soap_handler->event($message, $sender_soap_id)) {
  CAppUI::stepAjax("Le fichier n'a pu être dispatché correctement", UI_MSG_ERROR);
  
  mbTrace($ack);
}

?>