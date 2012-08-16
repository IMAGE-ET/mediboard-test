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

<script type="text/javascript">
  countChecked = function(message) {
    var tab = $(message).select("input[value=1]:checked");
    $("span-"+message).innerHTML = tab.length;
  }

  Main.add(function () {
    Control.Tabs.create('tabs-messages-supported', true);
    
    {{foreach from=$messages key=_message item=_messages_supported}}
      countChecked('{{$_message}}');
    {{/foreach}}
  });
</script>

<ul id="tabs-messages-supported" class="control_tabs">
  {{foreach from=$messages key=_message item=_messages_supported}}
    <li> 
      <a href="#{{$_message}}"> {{tr}}{{$_message}}{{/tr}} (<span id="span-{{$_message}}">-</span>/{{$_messages_supported|@count}}) </a> 
    </li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$messages key=_message item=_messages_supported}}
<div id="{{$_message}}" style="display: none;"> 
  <table class="tbl form">
    {{foreach from=$_messages_supported item=_message_supported}}
    <tr>
      <th width="20%">{{tr}}{{$_message_supported->message}}{{/tr}}</th>
      <td>
        {{unique_id var=uid}}
        <form name="editActorMessageSupported-{{$uid}}" 
          action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete:countChecked.curry('{{$_message}}')});">
          <input type="hidden" name="m" value="eai" />
          <input type="hidden" name="dosql" value="do_message_supported" />
          <input type="hidden" name="del" value="0" />  
          <input type="hidden" name="message_supported_id" value="{{$_message_supported->_id}}" />
          <input type="hidden" name="object_id" value="{{$_message_supported->object_id}}" />
          <input type="hidden" name="object_class" value="{{$_message_supported->object_class}}" />
          <input type="hidden" name="message" value="{{$_message_supported->message}}" />
          
          {{mb_field object=$_message_supported field=active onchange="this.form.onsubmit();"}}
        </form>
      </td>
    </tr>
    {{/foreach}}
  </table>
</div>
{{/foreach}}