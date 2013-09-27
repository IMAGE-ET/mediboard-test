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

$ticket_name         = CValue::getOrSession("ticket_name");
$_creation_date_min  = CValue::getOrSession("_creation_date_min");
$_creation_date_max  = CValue::getOrSession("_creation_date_max");
$_due_date_min       = CValue::getOrSession("_due_date_min");
$_due_date_max       = CValue::getOrSession("_due_date_max");
$_closing_date_min   = CValue::getOrSession("_closing_date_min");
$_closing_date_max   = CValue::getOrSession("_closing_date_max");
$assigned_to_id      = CValue::getOrSession("assigned_to_id", CAppUI::$user->_id);
$supervisor_id       = CValue::getOrSession("supervisor_id");
$_status             = CValue::getOrSession("_status", "new|accepted|inprogress");
$_type               = CValue::getOrSession("_type");
$_priority           = CValue::getOrSession("_priority");
$_funding            = CValue::getOrSession("_funding");
$estimate            = CValue::getOrSession("estimate");

$no_assigned_to      = CValue::getOrSession("no_assigned_to");
$no_supervisor       = CValue::getOrSession("no_supervisor");
$relative            = CValue::getOrSession("relative",  "on");
$toggle              = CValue::getOrSession("toggle",    "0");
$order_col           = CValue::getOrSession("order_col");
$order_way           = CValue::getOrSession("order_way");
$tags_to_search      = CValue::getOrSession("tags_to_search");
$select_estimate     = CValue::getOrSession("select_estimate", "=");

$ticket = new CTaskingTicket();
$spec   = $ticket->_spec;
$ds     = $spec->ds;

if ($assigned_to_id) {
  $ticket->assigned_to_id = $assigned_to_id;
  $ticket->loadRefAssignedToUser();
}

if ($supervisor_id) {
  $ticket->supervisor_id = $supervisor_id;
  $ticket->loadRefSupervisorUser();
}

$tags = array();
if ($tags_to_search) {
  $tag_ids = explode("|", $tags_to_search);
  foreach ($tag_ids as $_tag_id) {
    $tag = new CTag();
    $tag->load($_tag_id);

    if ($tag->_id) {
      $tags[$_tag_id] = $tag;
    }
  }
}

$ticket->ticket_name        = $ticket_name;
$ticket->_creation_date_min = $_creation_date_min;
$ticket->_creation_date_max = $_creation_date_max;
$ticket->_due_date_min      = $_due_date_min;
$ticket->_due_date_max      = $_due_date_max;
$ticket->_closing_date_min  = $_closing_date_min;
$ticket->_closing_date_min  = $_closing_date_max;
$ticket->_status            = $_status;
$ticket->_type              = $_type;
$ticket->_priority          = $_priority;
$ticket->_funding           = $_funding;
$ticket->estimate           = $estimate;

$smarty = new CSmartyDP();
$smarty->assign('ticket',          $ticket);
$smarty->assign('no_assigned_to',  $no_assigned_to);
$smarty->assign('no_supervisor',   $no_supervisor);
$smarty->assign('relative',        $relative);
$smarty->assign('toggle',          $toggle);
$smarty->assign('order_col',       $order_col);
$smarty->assign('order_way',       $order_way);
$smarty->assign('tags_to_search',  $tags_to_search);
$smarty->assign("tags",            $tags);
$smarty->assign("select_estimate", $select_estimate);
$smarty->display('vw_tasks.tpl');