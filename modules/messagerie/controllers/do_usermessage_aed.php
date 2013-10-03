<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author Thomas despoix
 */

$dest_list = explode("|", CValue::post("to_list"));

//single send
if (count($dest_list) <= 1) {
  $do = new CDoObjectAddEdit("CUserMessage", "usermessage_id");
  $do->doIt();
}
//multi send
else {
  foreach ($dest_list as $_dest) {
    $userMessage = new CUserMessage();
    $userMessage->to = $_dest;
    $userMessage->from = CValue::post("from");
    $userMessage->subject = CValue::post("subject");
    $userMessage->source = CValue::post("source");
    $userMessage->date_sent = CMbDT::dateTime();
    if ($msg = $userMessage->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
  CAppUI::redirect(CValue::post("postRedirect"));
}
