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

$tasking_ticket_id = CValue::get("tasking_ticket_id");

$tasking_ticket = new CTaskingTicket();

if ($tasking_ticket_id) {
  $tasking_ticket->load($tasking_ticket_id);
  $tasking_ticket->loadRefAssignedToUser();
  $tasking_ticket->loadRefSupervisorUser();
  $tasking_ticket->loadRefsTags();
  //$tasking_ticket->loadRefBill();

  if ($tasking_ticket->loadRefsMessages()) {
    foreach ($tasking_ticket->_ref_tasking_messages as $_message) {
      $_message->loadAuthorView();
    }
  }
}
else {
  // New CTaskingTicket
  $task_smart = CValue::get("task_smart");

  $task_smart = (empty($task_smart)) ? "Nom de votre tâche" : $task_smart;

  $tasking_ticket->creation_date  = CMbDT::dateTime();
  $tasking_ticket->status         = "new";
  /*
  $tasking_ticket->assigned_to_id = CAppUI::$user->_id;
  $tasking_ticket->supervisor_id  = CAppUI::$user->_id;
  $tasking_ticket->loadRefAssignedToUser();
  $tasking_ticket->loadRefSupervisorUser();
  */

  $tags    = array();
  $matches = array();
  if (preg_match_all("/(!\d|\^\d\d\/\d\d\/\d\d\d\d|#[A-z0-9]+|=\d+|@ref|@bug|@erg|@fnc|@action)/", $task_smart, $matches)) {
    foreach ($matches[1] as $_match) {
      switch (substr($_match, 0, 1)) {
        // Priority
        case "!":
          $priority = substr($_match, 1);
          $tasking_ticket->priority = ($priority >= 0 && $priority <= 3) ? $priority : null;
          break;

        // Type
        case "@":
          $tasking_ticket->type = substr($_match, 1);
          break;

        // Tag
        case "#":
          $tags[] = substr($_match, 1);
          break;

        // Due date
        case "^":
          $tasking_ticket->due_date = CMbDT::dateTime(CMbDT::dateFromLocale(substr($_match, 1)));
          break;

        // Estimate
        case "=":
          $tasking_ticket->estimate = substr($_match, 1);
      }
    }

    $task_smart = trim(preg_replace("/(!\d|\^\d\d\/\d\d\/\d\d\d\d|#[A-z0-9]+|=\d+|@ref|@bug|@erg|@fnc|@action)/", "", $task_smart));
  }

  $tasking_ticket->ticket_name = (empty($task_smart)) ? "Nom de votre tâche" : $task_smart;

  if ($msg = $tasking_ticket->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }

  foreach ($tags as $_tag) {
    $tag = new CTag();
    $tag->name = $_tag;
    $tag->object_class = "CTaskingTicket";
    $tag->loadMatchingObject();

    if (!$tag->_id) {
      if ($msg = $tag->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }

    // We have to create the link between the CTag and the CTaskingTicket => CTagItem
    $tag_item = new CTagItem();

    $tag_item->tag_id       = $tag->_id;
    $tag_item->object_id    = $tasking_ticket->_id;
    $tag_item->object_class = "CTaskingTicket";

    if ($msg = $tag_item->store()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
  }

  $tasking_ticket->loadRefsTags();
}

$smarty = new CSmartyDP();
$smarty->assign("tasking_ticket", $tasking_ticket);
$smarty->display("inc_edit_tasking_ticket.tpl");
