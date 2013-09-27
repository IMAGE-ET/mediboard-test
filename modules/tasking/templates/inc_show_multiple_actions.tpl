{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<div>
  <button type="button" class="tick singleclick" onclick="Tasking.multipleTaskingTickets('close');">
    {{tr}}CTaskingTicket-task-close{{/tr}}
  </button>

  <button type="button" class="change singleclick" onclick="Tasking.multipleTaskingTickets('postpone');">
    {{tr}}CTaskingTicket-task-postpone{{/tr}}
  </button>

  <select onchange="Tasking.multipleTaskingTickets($V(this)); this.selectedIndex = 0;">
    <option value="">{{tr}}More-Actions{{/tr}}...</option>
    <option value="delete">{{tr}}Delete{{/tr}}</option>
    <option disabled=true>---</option>
    <option disabled=true>{{tr}}CTaskingTicket-priority{{/tr}}...</option>
    <option value="priority_1">..{{tr}}CTaskingTicket.priority.1{{/tr}}</option>
    <option value="priority_2">..{{tr}}CTaskingTicket.priority.2{{/tr}}</option>
    <option value="priority_3">..{{tr}}CTaskingTicket.priority.3{{/tr}}</option>
    <option value="priority_0">..{{tr}}CTaskingTicket.priority.0{{/tr}}</option>
    <option value="priority_+">..{{tr}}CTaskingTicket-priority+1{{/tr}}</option>
    <option value="priority_-">..{{tr}}CTaskingTicket-priority-1{{/tr}}</option>
    <option disabled=true>---</option>
    <option disabled=true>{{tr}}CTaskingTicket-status{{/tr}}...</option>
    <option value="status_new">..{{tr}}CTaskingTicket.status.new{{/tr}}</option>
    <option value="status_accepted">..{{tr}}CTaskingTicket.status.accepted{{/tr}}</option>
    <option value="status_inprogress">..{{tr}}CTaskingTicket.status.inprogress{{/tr}}</option>
    <option value="status_invalid">..{{tr}}CTaskingTicket.status.invalid{{/tr}}</option>
    <option value="status_duplicate">..{{tr}}CTaskingTicket.status.duplicate{{/tr}}</option>
    <option value="status_cancelled">..{{tr}}CTaskingTicket.status.cancelled{{/tr}}</option>
    <option value="status_closed">..{{tr}}CTaskingTicket.status.closed{{/tr}}</option>
    <option value="status_refused">..{{tr}}CTaskingTicket.status.refused{{/tr}}</option>
    <option disabled=true>---</option>
    <option disabled=true>{{tr}}CTaskingTicket-type{{/tr}}...</option>
    <option value="type_ref">..{{tr}}CTaskingTicket.type.ref{{/tr}}</option>
    <option value="type_bug">..{{tr}}CTaskingTicket.type.bug{{/tr}}</option>
    <option value="type_erg">..{{tr}}CTaskingTicket.type.erg{{/tr}}</option>
    <option value="type_fnc">..{{tr}}CTaskingTicket.type.fnc{{/tr}}</option>
    <option value="type_action">..{{tr}}CTaskingTicket.type.action{{/tr}}</option>
  </select>
</div>