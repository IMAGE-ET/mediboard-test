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
    Control.Tabs.create('tabs-interop-norm-domains', true);
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
      <ul id="tabs-interop-norm-domains" class="control_tabs_vertical">
        {{foreach from=$all_messages key=_domain item=_families}}
          <li style="width: 260px">
            <a href="#{{$_domain}}">
              {{tr}}CInteropNorm_{{$_domain}}{{/tr}}
              <br />
              <span class="compact">{{tr}}CInteropNorm_{{$_domain}}-desc{{/tr}}</span>
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td style="vertical-align: top; width: 100%">
      {{foreach from=$all_messages key=_domain_name item=_domains}}
        <div id="{{$_domain_name}}" style="display: none;">
          <script type="text/javascript">
            Control.Tabs.create('tabs-'+'{{$_domain_name}}'+'-families', true);
          </script>

          <ul id="tabs-{{$_domain_name}}-families" class="control_tabs">
            {{foreach from=$_domains item=_families}}
              {{assign var=_family_name value=$_families|get_class}}

              <li style="width: 260px">
                <a href="#{{$_family_name}}">
                  {{tr}}{{$_family_name}}{{/tr}}
                  <br />
                  <span class="compact">{{tr}}{{$_family_name}}-desc{{/tr}}</span>
                </a>
              </li>
            {{/foreach}}
          </ul>

          <hr />

          {{foreach from=$_domains item=_families}}
            {{assign var=_family_name value=$_families|get_class}}

            <div id="{{$_family_name}}" style="display: none;">
              <table class="tbl form" id>
                {{foreach from=$_families->_categories key=_category_name item=_messages_supported}}
                  {{if $_category_name != "none"}}
                    <tr>
                      <th class="section" colspan="3">
                        <button class="tick notext" onclick="checkAll('{{$_category_name}}', '{{$_message}}')"></button>
                        {{tr}}{{$_category_name}}{{/tr}} (<em>{{$_category_name}})</em></th>
                    </tr>
                  {{/if}}

                  {{foreach from=$_messages_supported item=_message_supported}}
                    <tr>
                      <td style="width: 20%"><strong>{{tr}}{{$_message_supported->message}}{{/tr}}</strong></td>
                      <td>
                        {{unique_id var=uid}}
                        <form name="editActorMessageSupported-{{$uid}}"
                              action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete:countChecked.curry('{{$_family_name}}')});">
                          <input type="hidden" name="m" value="eai" />
                          <input type="hidden" name="dosql" value="do_message_supported" />
                          <input type="hidden" name="del" value="0" />
                          <input type="hidden" name="message_supported_id" value="{{$_message_supported->_id}}" />
                          <input type="hidden" name="object_id" value="{{$_message_supported->object_id}}" />
                          <input type="hidden" name="object_class" value="{{$_message_supported->object_class}}" />
                          <input type="hidden" name="message" value="{{$_message_supported->message}}" />
                          <input type="hidden" name="profil" value="{{$_family_name}}" />

                          {{if $_category_name && $_category_name != "none"}}
                            <input type="hidden" name="transaction" value="{{$_category_name}}" />
                          {{/if}}

                          {{mb_field object=$_message_supported class=switch_on_$_family_name$_category_name field=active onchange="this.form.onsubmit();"}}
                        </form>
                      </td>
                      <td class="text compact">{{tr}}{{$_message_supported->message}}-desc{{/tr}}</td>
                    </tr>
                  {{/foreach}}
                {{/foreach}}
              </table>
            </div>
          {{/foreach}}
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>