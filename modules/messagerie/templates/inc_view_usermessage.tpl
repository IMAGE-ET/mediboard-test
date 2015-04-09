{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<div id="actions" style="text-align: center; margin-bottom: 5px;">
  {{if $mode == 'inbox'}}
    <button type="button" onclick="Control.Modal.close();UserMessage.create('{{$usermessage->creator_id}}', '{{$usermessage->_id}}', '{{$app->user_prefs.inputMode}}');">
      <i class="msgicon fa fa-reply"></i>
      {{tr}}CUserMessage.answer{{/tr}}
    </button>

    {{if $usermessage->_ref_destinataires|@count > 1}}
      <button type="button" onclick="Control.Modal.close();UserMessage.create('{{$usermessage->creator_id}}', '{{$usermessage->_id}}', '{{$app->user_prefs.inputMode}}', 1);">
        <i class="msgicon fa fa-reply-all"></i>
        {{tr}}CUserMessage.answer_to_all{{/tr}}
      </button>
    {{/if}}

    <button type="button" title="{{tr}}CUserMessageDest-title-to_archive-0{{/tr}}" onclick="UserMessage.editAction('archive', '1', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-archive"></i>
      {{tr}}CUserMessageDest-title-to_archive-0{{/tr}}
    </button>
  {{/if}}

  {{if $mode == 'archive'}}
    <button type="button" title="{{tr}}CUserMessageDest-title-to_archive-1{{/tr}}" onclick="UserMessage.editAction('archive', '0', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-inbox"></i>
      {{tr}}CUserMessageDest-title-to_archive-1{{/tr}}
    </button>
  {{/if}}

  {{if $mode != 'sentbox'}}
    <button type="button" title="{{tr}}Delete{{/tr}}" onclick="UserMessage.editAction('delete', '', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-trash"></i>
      {{tr}}Delete{{/tr}}
    </button>
  {{/if}}

  {{if $mode == 'inbox'}}
    <button type="button" title="{{tr}}CUserMessage-title-read{{/tr}}" onclick="UserMessage.editAction('mark_read', '', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-eye"></i>
      {{tr}}CUserMessageDest-title-read{{/tr}}
    </button>

    <button type="button" title="{{tr}}CUserMessage-title-unread{{/tr}}" onclick="UserMessage.editAction('mark_unread', '', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-eye-slash"></i>
      {{tr}}CUserMessageDest-title-unread{{/tr}}
    </button>

    <button type="button" title="{{tr}}CUserMessageDest-title-to_star-0{{/tr}}" onclick="UserMessage.editAction('star', '1', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-star"></i>
      {{tr}}CUserMessageDest-title-to_star-0{{/tr}}
    </button>

    <button type="button" title="{{tr}}CUserMessageDest-title-to_star-0{{/tr}}" onclick="UserMessage.editAction('star', '0', '{{$usermessage->_ref_dest_user->_id}}');">
      <i class="msgicon fa fa-star-o"></i>
      {{tr}}CUserMessageDest-title-to_star-1{{/tr}}
    </button>
  {{/if}}
</div>

<table class="form" style="width: 100%; margin-top: 5px; margin-bottom: 10px;">
  <tr>
    <th colspan="4" class="title">{{mb_value object=$usermessage field=subject}}</th>
  </tr>
  <tr>
    <th class="narrow">{{tr}}CUserMessageDest-from_user_id{{/tr}}</th>
    <td>
      <div class="mediuser" style="border-color: #{{$usermessage->_ref_user_creator->_ref_function->color}};">
        {{$usermessage->_ref_user_creator}}
      </div>
    </td>
    <th class="narrow">{{tr}}CUserMessageDest-to_user_id{{/tr}}</th>
    <td>
      <ul style="padding: 0px;">
        {{foreach from=$usermessage->_ref_destinataires item=_dest}}
          <li id="dest_{{$_dest->_ref_user_to->_id}}" style="list-style: none;">
            <span class="mediuser" style="border-color: #{{$_dest->_ref_user_to->_ref_function->color}};">
              {{$_dest->_ref_user_to}}
            </span>
          </li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
  <tr>
    <th class="narrow">{{tr}}CUserMessageDest-datetime_sent{{/tr}}</th>
    <td>
      {{$usermessage->_ref_dest_user->_datetime_sent}}
    </td>
    <th class="narrow">{{tr}}CUserMessageDest-datetime_read{{/tr}}</th>
    <td>
      {{$usermessage->_ref_dest_user->_datetime_read}}
    </td>
  </tr>
</table>
<hr/>

<iframe id="message_content" src="?m=messagerie&amp;a=get_usermessage_content&amp;usermessage_id={{$usermessage->_id}}&amp;suppressHeaders=1"></iframe>