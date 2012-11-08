<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$message_string = CValue::post("message");

if (!$message_string) {
  return;
}

$message_string = stripslashes($message_string);

CValue::setSession("message", $message_string);

try {
  $message = new CHL7v2Message;
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
$smarty->display("inc_display_hl7v2_message.tpl");
