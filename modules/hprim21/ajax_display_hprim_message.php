<?php

/**
 * Affichage en ajax des messages Hprim21
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$message_string = CValue::post("message");

if (!$message_string) {
  return;
}

$message_string = stripslashes($message_string);

CValue::setSession("message", $message_string);

try {
  $message = new CHPrim21Message();
  $message->parse($message_string);
  
  $message->_errors_msg   = !$message->isOK(CHL7v2Error::E_ERROR);
  $message->_warnings_msg = !$message->isOK(CHL7v2Error::E_WARNING);
  $message->_xml = CMbString::highlightCode("xml", $message->toXML()->saveXML());
} catch (CHL7v2Exception $e) {
  CAppUI::stepMessage(UI_MSG_ERROR, $e->getMessage()." (".$e->extraData.")");
  return;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("message", $message);
$smarty->assign("key", "input");
$smarty->display("inc_display_hprim_message.tpl");
