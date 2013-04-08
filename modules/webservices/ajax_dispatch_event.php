<?php
/**
 * Dispatch event
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$sender_soap_id = CValue::get("sender_soap_id");
$message        = CValue::get("message");

$message = utf8_encode(stripcslashes($message));

$soap_handler = new CEAISoapHandler();
// Dispatch EAI 
if (!$ack = $soap_handler->event($message, $sender_soap_id)) {
  CAppUI::stepAjax("Le fichier n'a pu être dispatché correctement", UI_MSG_ERROR);
  
  mbTrace($ack);
}