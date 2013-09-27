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

if ($tasking_ticket_id) {
  $tasking_ticket = new CTaskingTicket();
  $tasking_ticket->load($tasking_ticket_id);

  if ($tasking_ticket->loadRefsMessages()) {
    foreach ($tasking_ticket->_ref_tasking_messages as $_message) {
      $_message->loadAuthorView();
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("tasking_ticket", $tasking_ticket);
$smarty->display("inc_list_tasking_ticket_messages.tpl");
