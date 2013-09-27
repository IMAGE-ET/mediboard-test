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

$action = CValue::get("action");
$tasks  = CValue::get("tasks");

switch ($action) {
  case "close":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = $task->closing_date = CMbDT::dateTime();
      $task->status       = "closed";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "postpone":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $today = CMbDT::dateTime();

      if ($task->due_date) {
        if ($task->due_date < $today) {
          $task->due_date = $today;
        }
        else {
          $task->due_date = CMbDT::dateTime("+1 day", $task->due_date);
        }
      }
      else {
        $task->due_date = $today;
      }

      $task->last_modification_date = $today;
      $task->nb_postponements++;

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "delete":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      if ($msg = $task->delete()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "priority_1":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();
      $task->priority = "1";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "priority_2":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();
      $task->priority = 2;

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "priority_3":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();
      $task->priority = 3;

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "priority_0":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();
      $task->priority = 0;

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "priority_+":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $priority = (int) $task->priority;

      if ($priority == 0) {
        $task->priority = 3;
      }
      elseif ($priority > 1) {
        $task->priority = $priority - 1;
      }

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "priority_-":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $priority = (int) $task->priority;

      if ($priority === 3) {
        $task->priority = 0;
      }
      elseif ($priority < 3 && $priority > 0) {
        $task->priority = $priority + 1;
      }

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_new":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "new";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_accepted":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "accepted";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_inprogress":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "inprogress";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_invalid":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "invalid";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_duplicate":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "duplicate";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_cancelled":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "cancelled";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_closed":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = $task->closing_date = CMbDT::dateTime();

      $task->status = "closed";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "status_refused":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->status = "refused";
      $task->closing_date = "";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "type_ref":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->type = "ref";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "type_bug":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->type = "bug";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "type_erg":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->type = "erg";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "type_fnc":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->type = "fnc";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
    break;

  case "type_action":
    $tasks = json_decode(stripslashes($tasks));

    foreach ($tasks as $_task_id) {
      $task = new CTaskingTicket();
      $task->load($_task_id);

      $task->last_modification_date = CMbDT::dateTime();

      $task->type = "action";

      if ($msg = $task->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
}
