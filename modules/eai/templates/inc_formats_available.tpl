{{*
 * Formats available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>{{tr}}CExchangeTabular{{/tr}}</legend>
  
  {{foreach from=$formats_tabular item=_format_tabular}}
    <button class="none" onclick="InteropActor.viewMessagesSupported('{{$actor_guid}}', '{{$_format_tabular}}')">
      {{tr}}{{$_format_tabular}}{{/tr}}
    </button>
  {{/foreach}}
  
  {{foreach from=$messages_tabular key=_message_tabular item=_messages_tabular_supported}}
    <fieldset>
      <legend>{{tr}}{{$_message_tabular}}{{/tr}}</legend>
      
      <table class="tbl">
      {{foreach from=$_messages_tabular_supported item=_message_tabular_supported}}
        <tr>
          <th class="category narrow">{{tr}}{{$_message_tabular_supported->message}}{{/tr}}</th>
          <td> {{mb_value object=$_message_tabular_supported field=active}} </td>
        </tr>
      {{/foreach}}
      </table>
      
    </fieldset>
  {{/foreach}}
</fieldset>

<fieldset> 
  <legend>{{tr}}CEchangeXML{{/tr}}</legend>
  
  {{foreach from=$formats_xml item=_format_xml}}
    <button class="none" onclick="InteropActor.viewMessagesSupported('{{$actor_guid}}', '{{$_format_xml}}')">
      {{tr}}{{$_format_xml}}{{/tr}}
    </button>
  {{/foreach}}
  
  {{foreach from=$messages_xml key=_message_xml item=_messages_xml_supported}}
    <fieldset>
      <legend>{{tr}}{{$_message_xml}}{{/tr}}</legend>
      
      <table class="tbl">
      {{foreach from=$_messages_xml_supported item=_message_xml_supported}}
        <tr>
          <th class="category narrow">{{tr}}{{$_message_xml_supported->message}}{{/tr}}</th>
          <td> {{mb_value object=$_message_xml_supported field=active}} </td>
        </tr>
      {{/foreach}}
      </table>
      
    </fieldset>
    {{/foreach}}
</fieldset>