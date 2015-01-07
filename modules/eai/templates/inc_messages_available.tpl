{{*
 * Messages available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=transformation ajax=true}}

<fieldset>
  <legend>{{tr}}{{$message}}{{/tr}} <span class="compact">({{tr}}{{$message}}-desc{{/tr}})</span></legend>
  
  <table class="tbl">
  {{foreach from=$messages_supported item=_message_supported}}
    {{assign var=event      value=$_message_supported->_event}}
    {{assign var=event_name value=$event|get_class}}

    <tr>
      <th class="section narrow" style="text-align: left">
        <button title="{{tr}}CEAITransformation{{/tr}}" class="magic_wand notext"
                onclick="EAITransformation.list('{{$message}}', '{{$event_name}}', '{{$actor_guid}}')">
          {{tr}}CEAITransformation{{/tr}}</button>

        {{tr}}{{$_message_supported->message}}{{/tr}}
      </th>
      <td class="text compact">
        {{tr}}{{$_message_supported->message}}-desc{{/tr}}
      </td>
    </tr>
  {{/foreach}}
  </table>
</fieldset>
