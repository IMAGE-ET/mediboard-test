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

$label       = CValue::getOrSession("label");
$description = CValue::getOrSession("description");
$priority    = CValue::getOrSession("priority");
$due_date    = CValue::getOrSession("due_date");
$order_by    = CValue::getOrSession("order_by", "due_date");

$ticket = new CTicketRequest();
$spec   = $ticket->_spec;
$ds     = $spec->ds;

$smarty = new CSmartyDP();
$smarty->assign('ticket',   $ticket);
$smarty->assign('order_by', $order_by);
$smarty->display('vw_ticket_requests.tpl');