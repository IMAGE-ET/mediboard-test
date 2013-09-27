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

<tr class="{{$_tasking_ticket->_status_resume}}">
  <td class="narrow"><input type="checkbox" class="checkTask" data-id="{{$_tasking_ticket->_id}}" value="" /></td>
  <td class="narrow prio{{$_tasking_ticket->priority}}"></td>

  <td class="text">
    <span class="task-name task{{$_tasking_ticket->_due_date}}" style="cursor: pointer" onclick="Tasking.editTaskingTicket('{{$_tasking_ticket->_id}}')">
      {{mb_value object=$_tasking_ticket field=ticket_name}}

      {{foreach from=$_tasking_ticket->_ref_tags item=_tag}}
        <span class="tag_task" style="background-color: #{{$_tag->color}};">
          {{mb_value object=$_tag field=name}}
        </span>
      {{/foreach}}
    </span>
  </td>

  <td class="narrow type_{{$_tasking_ticket->type}}" style="text-align: center;">
    {{mb_value object=$_tasking_ticket field=type}}
  </td>
  <td class="narrow status_{{$_tasking_ticket->status}}" style="text-align: center;">
    {{mb_value object=$_tasking_ticket field=status}}
  </td>

  <td class="narrow">
    {{if $_tasking_ticket->_ref_tasking_messages}}
      <img src="images/icons/postit.png" onmouseover="ObjectTooltip.createDOM(this, 'task-{{$_tasking_ticket->_id}}', {duration: 0})"/>
      {{mb_include module=system template=inc_vw_counter_tip count=$_tasking_ticket->_ref_tasking_messages|@count}}
      <table id="task-{{$_tasking_ticket->_id}}" class="tbl" style="width: 350px; display: none">
        {{foreach from=$_tasking_ticket->_ref_tasking_messages item=_message}}
          {{if $_message->_user_view}}
            <tr>
              <th class="title">
                {{$_message->_user_view}}

                {{if $_message->creation_date}}
                -- <em>{{mb_value object=$_message field=creation_date}}</em>

                {{/if}}
              </th>
            </tr>
          {{/if}}
          {{if $_message->title}}
            <tr>
              <th class="category">
                {{"CMbString::makeUrlHyperlinks"|static_call:$_message->title|nl2br}}
              </th>
            </tr>
          {{/if}}
          <tr><td class="text">{{"CMbString::makeUrlHyperlinks"|static_call:$_message->text|nl2br}}</td></tr>
        {{/foreach}}
      </table>
    {{/if}}
  </td>

  {{if !$_tasking_ticket->estimate}}
    <td class="narrow warning"></td>
  {{else}}
    <td class="narrow">
      {{mb_value object=$_tasking_ticket field=estimate}}h
    </td>
  {{/if}}

  <td class="narrow">
    <span class="date{{$_tasking_ticket->_due_date}}">
      {{if $relative}}
        {{$_tasking_ticket->due_date|rel_datetime}}
      {{else}}
        {{mb_value object=$_tasking_ticket field=due_date}}
      {{/if}}
    </span>
  </td>
</tr>
