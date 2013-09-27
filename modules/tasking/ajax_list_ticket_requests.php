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

$start       = (int) CValue::get("start", 0);

$label       = CValue::get("label");
$description = CValue::get("description");
$priority    = CValue::get("priority");
$due_date    = CValue::get("due_date");
$order_by    = CValue::get("order_by", "due_date");

CValue::setSession("label",       $label);
CValue::setSession("description", $description);
CValue::setSession("priority",    $priority);
CValue::setSession("due_date",    $due_date);
CValue::setSession("order_by",    $order_by);

$ticket = new CTicketRequest();
$spec   = $ticket->_spec;
$ds     = $spec->ds;

$where = array();

if ($label) {
  $where['label'] = $ds->prepareLike("%$label%");
}

if ($description) {
  $where['description'] = $ds->prepareLike("%$description%");
}

if ($priority) {
  $where['priority'] = $ds->prepareLike($priority);
}

if ($due_date) {
  $where[] = $ds->prepare("due_date >= %", $due_date);
}

$limit = "$start, 30";

$error_logs = $ticket->loadList($where, $order_by, $limit);

$smarty = new CSmartyDP();
$smarty->assign('ticket',   $ticket);
$smarty->assign('order_by', $order_by);
$smarty->display('vw_ticket_requests.tpl');