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

CApp::setTimeLimit(360);

$file    = CValue::files('datafile');
$user_id = CValue::post("user_id");

if (!$file) {
  $msg = "Pas de fichier.";
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

$user = null;
if ($user_id) {
  $user = new CMediusers();
  $user->load($user_id);
}

$dom = new CMbXMLDocument();

if (!$dom->load($file['tmp_name'])) {
  CAppUI::redirect('m=tasking&tab=vw_import');
}

$xpath = new CMbXPath($dom);

$list_node = $xpath->queryUniqueNode("//list");
if ($list_node->nodeName != "list") {
  CAppUI::redirect('m=tasking&tab=vw_import');
}

$taskseries_nodes = $xpath->query("//taskseries");

$ds = CSQLDataSource::get("std");

// We need to build an array containing many combinations in order to retrieve the users which noted
$query = "SELECT `user_id`, `user_first_name`, `user_last_name`
          FROM `users`
          WHERE `template` = '0';";
$mediusers = $ds->loadList($query);

$users = array();
foreach ($mediusers as $_mediuser) {
  $first_name_up   = CMbString::upper($_mediuser["user_first_name"]);
  $last_name_up    = CMbString::upper($_mediuser["user_last_name"]);
  $first_name_a_up = CMbString::removeAccents($first_name_up);
  $last_name_a_up  = CMbString::removeAccents($last_name_up);

  $users[$first_name_up   . " " . $last_name_up]    = $_mediuser['user_id'];
  $users[$last_name_up    . " " . $first_name_up]   = $_mediuser['user_id'];
  $users[$first_name_a_up . " " . $last_name_a_up]  = $_mediuser['user_id'];
  $users[$last_name_a_up  . " " . $first_name_a_up] = $_mediuser['user_id'];
}
unset($mediusers);

// taskseries + task nodes => CTaskingTicket
foreach ($taskseries_nodes as $_taskseries_node) {
  $ticket = new CTaskingTicket();
  $ticket->ticket_name            = stripslashes(utf8_decode($_taskseries_node->getAttribute("name")));

  $last_modification_date = $_taskseries_node->getAttribute("modified");
  if ($last_modification_date) {
    $ticket->last_modification_date = CMbDT::format($last_modification_date, "%Y-%m-%d %H:%M:%S");
  }

  $task_node = $xpath->queryUniqueNode("task", $_taskseries_node);

  $due_date = $task_node->getAttribute("due");
  if ($due_date) {
    $ticket->due_date = CMbDT::format($due_date, "%Y-%m-%d %H:%M:%S");
  }

  $creation_date = $task_node->getAttribute("added");
  if ($creation_date) {
    $ticket->creation_date = CMbDT::format($creation_date, "%Y-%m-%d %H:%M:%S");
  }

  // Used afterwards
  $closing_date = $task_node->getAttribute("completed");

  // JSON closing date note must be used instead
  //$ticket->closing_date  = strtotime($task_node->getAttribute("completed"));

  $priority = $task_node->getAttribute("priority");
  $ticket->priority = ($priority == "N") ? 0 : (int) $priority;

  $estimate = str_replace("h", "", $task_node->getAttribute("estimate"));
  $ticket->estimate = ($estimate) ? (int) $estimate : null;

  $ticket->nb_postponements = (int) $task_node->getAttribute("postponed");

  if ($user) {
    $ticket->assigned_to_id = $user->_id;
    $ticket->supervisor_id  = $user->_id;
  }

  // In order to be able to store it and to get the last inserted ID
  $ticket->status = "new";

  if ($msg = $ticket->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
    continue;
  }

  $ticket->status = null;

  // Tags may concern: bills, status, type, funding, etc.
  $tags_node = $xpath->queryUniqueNode("tags", $_taskseries_node);
  $tag_nodes = $xpath->query(".//tag", $tags_node);
  foreach ($tag_nodes as $_tag_node) {
    $tag_value = CMbString::lower(utf8_decode($_tag_node->nodeValue));

    // It's a bill
    if (preg_match("/^fa-(.+)$/i", $tag_value)) {
      $bill = new CTaskingBill();
      $bill->bill_name = utf8_decode($_tag_node->nodeValue);

      if ($msg = $bill->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }

      $ticket->bill_id = $bill->_id;
    }
    else {
      switch ($tag_value) {
        case "fund-ox":
        case "fund-50":
        case "fund-cus":
          // Task Funding
          $ticket->funding = $tag_value;
          break;

        case "ref":
        case "fnc":
        case "erg":
        case "bug":
        case "action":
          // Ticket type
          $ticket->type = $tag_value;
          break;

        case "encours":
          $ticket->status = "inprogress";
          break;

        case "duplicate":
        case "cancelled":
          $ticket->status = $tag_value;
          break;

        default:
          // Standard tag
          CTaskingTicket::checkImportTag($_tag_node->nodeValue, $ticket->_id);
      }
    }
  }

  // Task notes will be stored as CTaskingTicketMessage
  $notes_node = $xpath->queryUniqueNode("notes", $_taskseries_node);
  $note_nodes = $xpath->query(".//note", $notes_node);
  foreach ($note_nodes as $_note_node) {
    $note_title = stripslashes(utf8_decode($_note_node->getAttribute("title")));

    switch ($note_title) {
      case "data":
        // Manual cloture date case, JSON format
        $cloture_date = stripslashes($_note_node->nodeValue);
        $cloture_date = json_decode($cloture_date, true);

        if (isset($cloture_date["date_cloture"])) {
          // Task may have been closed and re-opened
          if ($closing_date) {
            $ticket->closing_date = CMbDT::format($cloture_date["date_cloture"], "%Y-%m-%d %H:%M:%S");

            if (!$ticket->status) {
              $ticket->status = "closed";
            }
          }
        }
        break;

      default:
        // Possible formats: "Author - Date", "Author", "Note title", ""
        $note = new CTaskingTicketMessage();

        $note_creation_date = $_note_node->getAttribute("created");
        if ($note_creation_date) {
          $note->creation_date = CMbDT::format($note_creation_date, "%Y-%m-%d %H:%M:%S");
        }

        $matches = array();
        if (preg_match("/^(.+) - (\d{4}-\d\d-\d\d \d\d:\d\d:\d\d|\d\d-\d\d-\d{4} \d\d:\d\d:\d\d)/", $note_title, $matches)) {
          // Format: "Author - Date"
          $note->creation_date = CMbDT::format($matches[2], "%Y-%m-%d %H:%M:%S");

          $_user = explode(' ', utf8_decode($matches[1]), 2);

          $query = "SELECT `user_id`
                    FROM   `users`
                    WHERE  `user_last_name`  = '" . CMbString::upper($_user[0]) . "'
                    AND    `user_first_name` = '" . CMbString::upper($_user[1]) . "'";

          // In case where the first and last name are inverted
          if (!$_user_id = $ds->loadResult($query)) {
            $query = "SELECT `user_id`
                      FROM   `users`
                      WHERE  `user_first_name` = '" . CMbString::upper($_user[0]) . "'
                      AND    `user_last_name`  = '" . CMbString::upper($_user[1]) . "'";

            if (!$_user_id = $ds->loadResult($query)) {
              // Note is self-attributed
              $_user_id = $user_id;
            }
          }

          $note->user_id = $_user_id;
        }
        else {
          // Format: "Author" or "Note title" or ""
          // If "Author", probably in this array, else self-attributed
          $note_title_up = CMbString::upper($note_title);
          if (array_key_exists($note_title_up, $users)) {
            $note->user_id = $users[$note_title_up];
          }
          else {
            $note->user_id = $user_id;
            $note->title = $note_title;
          }
        }

        $note->text = stripslashes(utf8_decode($_note_node->nodeValue));
        $note->task_id = $ticket->_id;

        if ($msg = $note->store()) {
          CAppUI::setMsg($msg, UI_MSG_WARNING);
        }
    }
  }

  if (!$ticket->closing_date && !$ticket->status) {
    if ($closing_date) {
      $ticket->closing_date = CMbDT::format($closing_date, "%Y-%m-%d %H:%M:%S");
      $ticket->status       = "closed";
    }
    else {
      $ticket->status = "accepted";
    }
  }

  if ($msg = $ticket->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
    continue;
  }
}

CAppUI::redirect('m=tasking&tab=vw_import');