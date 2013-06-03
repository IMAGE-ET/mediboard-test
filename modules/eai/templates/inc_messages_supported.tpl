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

  checkAll = function(type, message) {

    $$("input.switch_on_"+message+type+"[value=1]").each(function(checkbox) {
        checkbox.checked = true
        checkbox.form.onsubmit()
      });
  }


</script>

<table>
  <tr>
    <td style="vertical-align: top;">
      <ul id="tabs-messages-supported" class="control_tabs_vertical">
        {{foreach from=$messages key=_message item=_messages_supported}}
          <li style="width: 260px">
            <a href="#{{$_message}}">
              {{tr}}{{$_message}}{{/tr}} (<span id="span-{{$_message}}">-</span>/{{$_messages_supported|@count}})
              <br />
              <span class="compact">{{tr}}{{$_message}}-desc{{/tr}}</span>
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td style="vertical-align: top; width: 100%">
      {{foreach from=$all_messages key=_message item=_categories}}
        <div id="{{$_message}}" style="display: none;">
          <table class="tbl form">
            <tr>
              <th class="title" colspan="3">{{tr}}CEAI-trigger-events{{/tr}}</th>
            </tr>
            {{foreach from=$_categories key=_category_name item=_messages_supported}}
              {{if $_category_name != "none"}}
                <tr>
                  <th class="category" colspan="3">
                    <button class="tick notext" onclick="checkAll('{{$_category_name}}', '{{$_message}}')"></button>
                    {{tr}}{{$_category_name}}{{/tr}} ({{$_category_name}})</th>
                </tr>
              {{/if}}
              {{foreach from=$_messages_supported item=_message_supported}}
              <tr>
                <td style="width: 20%"><strong>{{tr}}{{$_message_supported->message}}{{/tr}}</strong></td>
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

                    {{mb_field object=$_message_supported class=switch_on_$_message$_category_name field=active onchange="this.form.onsubmit();"}}
                  </form>
                </td>
                <td class="text compact">{{tr}}{{$_message_supported->message}}-desc{{/tr}}</td>
              </tr>
              {{/foreach}}
            {{/foreach}}
          </table>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>