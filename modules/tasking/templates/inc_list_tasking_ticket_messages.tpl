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

<table class="main form">
  <tr>
    <th class="category" colspan="4">
      {{tr}}CTaskingTicketMessages{{/tr}}
      <button type="button" class="add notext singleclick" onclick="Tasking.Message.editMessage('{{$tasking_ticket->_id}}', '');">
        {{tr}}CTaskingTicketMessage-add{{/tr}}
      </button>
    </th>
  </tr>
  <tr>
    <td colspan="4">
      <div style="overflow-y: auto;">
        <table class="layout">
          {{foreach from=$tasking_ticket->_ref_tasking_messages item=_message}}
            <tr id="message-{{$_message->_id}}">
              <td class="narrow">
                <button type="button" class="edit notext singleclick" onclick="Tasking.Message.editMessage('{{$tasking_ticket->_id}}', '{{$_message->_id}}');">
                  {{tr}}Modify{{/tr}}
                </button>
              </td>
              <td style="white-space: normal;">
                {{if $_message->_user_view}}
                  <div class="empty">
                    -- {{$_message->_user_view}}{{if $_message->creation_date}}, {{mb_value object=$_message field=creation_date}}{{/if}}
                  </div>

                  {{if $_message->title}}
                    <strong>{{"CMbString::makeUrlHyperlinks"|static_call:$_message->title|nl2br}}</strong>
                  {{/if}}
                {{elseif $_message->title}}
                  <div class="empty">-- {{"CMbString::makeUrlHyperlinks"|static_call:$_message->title|nl2br}}{{if $_message->creation_date}}, {{mb_value object=$_message field=creation_date}}{{/if}}</div>
                {{elseif $_message->creation_date}}
                  {{mb_value object=$_message field=creation_date}}
                {{/if}}
                <div class="text view" style="width: 60%;">{{"CMbString::makeUrlHyperlinks"|static_call:$_message->text|nl2br}}</div>
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td colspan="4" class="empty">{{tr}}CTaskingTicketMessage-none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
</table>
