<?php 

/**
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$tasking_ticket_message_id = CValue::get("message_id");
$tasking_ticket_id         = CValue::get("task_id");

$message = new CTaskingTicketMessage();

if ($tasking_ticket_message_id) {
  $message->load($tasking_ticket_message_id);
}
else {
  // New CTaskingTicketMessage
  $message->creation_date = CMbDT::dateTime();
  $message->user_id       = CAppUI::$user->_id;
}

$smarty = new CSmartyDP();
$smarty->assign("message", $message);
$smarty->assign("task_id", $tasking_ticket_id);
$smarty->display("inc_edit_tasking_ticket_message.tpl");
