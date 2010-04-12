{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("php-config-tabs", true);
  
  $$(".edit-value input[type=checkbox]").each(function(checkbox){
    toggleInput($(checkbox).previous(), checkbox.checked);
  });
  
  toggleType('locked', $V($("show-locked")));
  toggleType('minor', $V($("show-minor")));
});

function toggleInput(input, value) {
  $(input)[value ? "enable" : "disable"]().setOpacity(value ? 1 : 0.5);

}

function toggleType(type, value) {
  $$('#php-config tr.'+type).invoke('setVisible', value);
  
  $$('#php-config-tabs a').each(function(a){
    var id = Url.parse(a.href).fragment,
        count = $(id).select("tr.edit-value").findAll(function(el){return el.visible()}).length;
        
    a.select('small')[0].update("("+count+")");
    a[count == 0 ? "addClassName" : "removeClassName"]("empty");
  });
}
</script>

<style type="text/css">
tr.important th {
  font-weight: bold;
}
</style>

<form name="editPHPConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <div class="small-warning">
    Utilisez cet outil avec une grande prudence, une mauvaise configuration peut avoir des effets irréversibles sur les données enregistrées.
  </div>
  
  <label><input type="checkbox" onclick="toggleType('minor', this.checked)" id="show-minor" /> Valeurs mineures</label>
  <label><input type="checkbox" onclick="toggleType('locked', this.checked)" id="show-locked" /> Valeurs verrouillées</label>
  
  <table>
    <tr>
      <td style="vertical-align: top;">
        <ul class="control_tabs_vertical" id="php-config-tabs">
        {{foreach from=$php_config item=section key=name}}
          <li><a href="#php-{{$name}}" style="padding: 1px 4px;">{{$name}} <small></small></a></li>
        {{/foreach}}
        </ul>
        <div style="text-align: right;">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        </div>
      </td>
      <td style="vertical-align: top;">
        <table class="form">
          <tr>
            <th class="category"></th>
            <th class="category">global</th>
            <th class="category">local</th>
          </tr>
          {{foreach from=$php_config item=section key=name}}
            <tbody id="php-{{$name}}" style="display: none;">
            {{foreach from=$section item=value key=key}}
              {{assign var=access value=$value.user}}
              <tr class="edit-value {{if !$access}}locked{{/if}} {{if in_array($key, $php_config_important)}}important{{else}}minor{{/if}}">
                <th>{{$key}}</th>
                <td class="text">{{$value.global_value}}</td>
                <td>
                  {{if $access}}
									  <input type="hidden" name="php[{{$key}}]" value=""/>
                    <input type="text" name="php[{{$key}}]" value="{{$value.local_value}}" disabled="disabled" style="opacity: 0.5;" />
                    <input type="checkbox" onclick="toggleInput($(this).previous(), this.checked); this.checked ? (this.previous()).previous().disabled='disabled' : (this.previous()).previous().removeAttribute('disabled');" {{if array_key_exists($key, $dPconfig.php) && $dPconfig.php.$key !== ""}}checked="checked"{{/if}} />
                  {{else}}
                    <input type="text" value="{{$value.local_value}}" readonly="readonly" disabled="disabled" />
                  {{/if}}
                </td>
              </tr>
            {{/foreach}}
            </tbody>
          {{/foreach}}
        </table>
      </td>
    </tr>
  </table>
</form>