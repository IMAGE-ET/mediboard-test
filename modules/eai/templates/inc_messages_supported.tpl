{{*
 * Messages supported
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}


{{foreach from=$messages key=_message item=_messages_supported}}
<fieldset>
  <legend>{{tr}}{{$_message}}{{/tr}}</legend>
  
  <table class="tbl">
  {{foreach from=$_messages_supported item=_message_supported}}
    <tr>
      <th class="category narrow">{{tr}}{{$_message_supported->message}}{{/tr}}</th>
      <td>
        <form name="editActorMessageSupported-{{$_message_supported->_guid}}" 
          action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="m" value="eai" />
          <input type="hidden" name="dosql" value="do_message_supported" />
          <input type="hidden" name="del" value="0" />  
          <input type="hidden" name="message_supported_id" value="{{$_message_supported->_id}}" />
          <input type="hidden" name="object_id" value="{{$_message_supported->object_id}}" />
          <input type="hidden" name="object_class" value="{{$_message_supported->object_class}}" />
          <input type="hidden" name="message" value="{{$_message_supported->message}}" />
          
          {{mb_field object=$_message_supported field=active onchange="this.form.onsubmit()"}}
        </form>
      </td>
    </tr>
  {{/foreach}}
  </table>
  
</fieldset>
{{/foreach}}