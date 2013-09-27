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

<td colspan="4" class="button">
  {{if !$tasking_ticket->_id}}
    <button type="submit" class="save singleclick">
      {{tr}}Save{{/tr}}
    </button>
  {{else}}
    <button type="submit" class="edit singleclick">
      {{tr}}Edit{{/tr}}
    </button>
  {{/if}}

  <button type="button" class="tick singleclick" onclick="Tasking.closeTaskingTicket(this.form);">
    {{tr}}CTaskingTicket-task-close{{/tr}}
  </button>

  <button type="button" class="change singleclick" onclick="Tasking.postpone(this.form);">
    {{tr}}CTaskingTicket-task-postpone{{/tr}}
  </button>

  <button type="button" class="duplicate singleclick" onclick="Tasking.duplicate(this.form);">
    {{tr}}Duplicate{{/tr}}
  </button>

  <button type="button" class="trash singleclick"
          onclick="Tasking.confirmDeletion(this.form, {typeName:'la tâche', objName:'{{$tasking_ticket->_view|smarty:nodefaults|JSAttribute}}'});">
    {{tr}}Delete{{/tr}}
  </button>
</td>