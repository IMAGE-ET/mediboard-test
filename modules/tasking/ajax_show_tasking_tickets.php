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

$start               = (int) CValue::get("start", 0);

$ticket_name         = CValue::get("ticket_name");
$assigned_to_id      = CValue::get("assigned_to_id");
$supervisor_id       = CValue::get("supervisor_id");
$_creation_date_min  = CValue::get("_creation_date_min");
$_creation_date_max  = CValue::get("_creation_date_max");
$_due_date_min       = CValue::get("_due_date_min");
$_due_date_max       = CValue::get("_due_date_max");
$_closing_date_min   = CValue::get("_closing_date_min");
$_closing_date_max   = CValue::get("_closing_date_max");
$_status             = CValue::get("_status", "new|accepted|inprogress");
$_type               = CValue::get("_type");
$_priority           = CValue::get("_priority");
$_funding            = CValue::get("_funding");
$estimate            = CValue::get("estimate");

$no_assigned_to      = CValue::get("no-assigned_to");
$no_supervisor       = CValue::get("no-supervisor");
$relative            = CValue::get("relative");
$toggle              = CValue::get("toggle");
$order_col           = CValue::get("order_col");
$order_way           = CValue::get("order_way");
$tags_to_search      = CValue::get("tags_to_search");
$select_estimate     = CValue::get("select_estimate");

CValue::setSession("ticket_name",        $ticket_name);
CValue::setSession("assigned_to_id",     $assigned_to_id);
CValue::setSession("supervisor_id",      $supervisor_id);
CValue::setSession("_creation_date_min", $_creation_date_min);
CValue::setSession("_creation_date_max", $_creation_date_max);
CValue::setSession("_due_date_min",      $_due_date_min);
CValue::setSession("_due_date_max",      $_due_date_max);
CValue::setSession("_closing_date_min",  $_closing_date_min);
CValue::setSession("_closing_date_max",  $_closing_date_max);
CValue::setSession("_status",            $_status);
CValue::setSession("_type",              $_type);
CValue::setSession("_priority",          $_priority);
CValue::setSession("_funding",           $_funding);
CValue::setSession("estimate",           $estimate);

CValue::setSession("no_assigned_to",     $no_assigned_to);
CValue::setSession("no_supervisor",      $no_supervisor);
CValue::setSession("relative",           $relative);
CValue::setSession("toggle",             $toggle);
CValue::setSession("order_col",          $order_col);
CValue::setSession("order_way",          $order_way);
CValue::setSession("tags_to_search",     $tags_to_search);
CValue::setSession("select_estimate",    $select_estimate);

$ticket = new CTaskingTicket();
$spec   = $ticket->_spec;
$ds     = $spec->ds;

$where    = array();
$ljoin    = array();
$group_by = null;

if ($ticket_name) {
  $where['ticket_name'] = $ds->prepareLike("%$ticket_name%");
}

if ($no_assigned_to) {
  $where['assigned_to_id'] = $ds->prepare("IS NULL");
}
elseif ($assigned_to_id) {
  $where['assigned_to_id'] = $ds->prepare("= '$assigned_to_id'");
}

if ($no_supervisor) {
  $where['supervisor_id'] = $ds->prepare("IS NULL");
}
elseif ($supervisor_id) {
  $where['supervisor_id'] = $ds->prepare("= '$supervisor_id'");
}

if ($_creation_date_min) {
  $where[] = $ds->prepare("`creation_date` >= %", $_creation_date_min);
}

if ($_creation_date_max) {
  $where[] = $ds->prepare("`creation_date` <= %", $_creation_date_max);
}

if ($_due_date_min) {
  $where[] = $ds->prepare("`due_date` >= %", $_due_date_min);
}

if ($_due_date_max) {
  $where[] = $ds->prepare("`due_date` <= %", $_due_date_max);
}

if ($_closing_date_min) {
  $where[] = $ds->prepare("`closing_date` >= %", $_closing_date_min);
}

if ($_closing_date_max) {
  $where[] = $ds->prepare("`closing_date` <= %", $_closing_date_max);
}

if ($_status) {
  $where['status'] = $ds->prepareIn(explode("|", $_status));
}

if ($_type) {
  $where['type'] = $ds->prepareIn(explode("|", $_type));
}

if ($_priority != null) {
  $where['priority'] = $ds->prepareIn(explode("|", $_priority));
}

if ($_funding) {
  $_funds = explode("|", $_funding);
  $fund_no = CMbArray::removeValue("fund-no", $_funds);

  if (empty($_funds)) {
    $where['funding'] = " IS NULL";
  }
  else {
    $where['funding'] = $ds->prepareIn($_funds);

    if ($fund_no) {
      $where['funding'] .= " OR `funding` IS NULL";
    }
  }
}

if ($tags_to_search) {
  $ljoin["tag_item"] = "`tag_item`.`object_id` = `tasking_ticket`.`tasking_ticket_id`";

  $tag_ids = explode("|", $tags_to_search);
  $where["tag_item.object_class"] = "= 'CTaskingTicket'";
  $where["tag_item.tag_id"]       = $ds->prepareIn($tag_ids);

  $group_by = "`tasking_ticket`.`tasking_ticket_id`";
}

if (in_array($select_estimate, array("=", ">", ">=", "<", "<=", "!=")) && $estimate) {
  $where['estimate'] = $ds->prepare("$select_estimate '$estimate'");
}

switch ($order_col) {
  case "ticket_name":
    $order_by = "IF (`closing_date` && (`status` = 'closed'), `closing_date`, 1) DESC, `ticket_name`, IF (`priority`, `priority`, 4), CASE WHEN `due_date` IS NULL THEN 1 ELSE 0 END, `due_date`";
    break;

  case "due_date":
    $order_by = "IF (`closing_date` && (`status` = 'closed'), `closing_date`, 1) DESC, CASE WHEN `due_date` IS NULL THEN 1 ELSE 0 END, `due_date`, IF (`priority`, `priority`, 4), `ticket_name`";
    break;

  case "priority":
  default:
    $order_by = "IF (`closing_date` && (`status` = 'closed'), `closing_date`, 1) DESC, IF (`priority`, `priority`, 4), CASE WHEN `due_date` IS NULL THEN 1 ELSE 0 END, `due_date`, `ticket_name`";
}

$limit = "$start, 30";

$total = $ticket->countList($where, null, $ljoin);
$tasking_tickets = $ticket->loadList($where, $order_by, $limit, $group_by, $ljoin);

foreach ($tasking_tickets as $_ticket) {
  $_ticket->loadRefsTags();
  $_ticket->loadRefsMessages();

  foreach ($_ticket->_ref_tasking_messages as $_message) {
    $_message->loadAuthorView();
  }
}

$request = new CRequest();
$request->addSelect("SUM(`estimate`) as `estimate`, `type`");
$request->addTable("`tasking_ticket`");
$request->addLJoin($ljoin);
$request->addWhere($where);
$request->addGroup("`type`");

$estimations_per_type = $ds->loadList($request->getRequest());

$total_estimation = 0;
$estimations = array(
  "fnc"    => array("count" => null, "part" => null),
  "erg"    => array("count" => null, "part" => null),
  "ref"    => array("count" => null, "part" => null),
  "bug"    => array("count" => null, "part" => null),
  "action" => array("count" => null, "part" => null)
);
foreach ($estimations_per_type as $_estimation) {
  $total_estimation += $_estimation["estimate"];
  $estimations[$_estimation["type"]]["count"] += $_estimation["estimate"];
}

if ($total_estimation > 0) {
  foreach ($estimations as $_type => $_estimation) {
    $estimations[$_type]["part"] = ($_estimation["count"] / $total_estimation) * 100;
  }
}

$smarty = new CSmartyDP();
$smarty->assign('ticket',               $ticket);
$smarty->assign('order_by',             $order_by);
$smarty->assign("total",                $total);
$smarty->assign("start",                $start);
$smarty->assign("total_estimation",     $total_estimation);
$smarty->assign("estimations",          $estimations);
$smarty->assign("start",                $start);
$smarty->assign('tasking_tickets',      $tasking_tickets);
$smarty->assign('no_assigned_to',       $no_assigned_to);
$smarty->assign('no_supervisor',        $no_supervisor);
$smarty->assign('relative',             $relative);
$smarty->assign('toggle',               $toggle);
$smarty->assign('order_col',            $order_col);
$smarty->assign('order_way',            $order_way);
$smarty->assign('tags_to_search',       $tags_to_search);
$smarty->assign('select_estimate',      $select_estimate);
$smarty->display('inc_show_tasking_tickets.tpl');