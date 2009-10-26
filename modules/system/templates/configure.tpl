{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("config-tabs", true);
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

<ul class="control_tabs" id="config-tabs">
  <li><a href="#framework-config">Framework</a></li>
  <li><a href="#php-config">PHP</a></li>
</ul>
<hr class="control_tabs" />

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form" id="framework-config" style="display: none;">

  {{assign var="category" value="ui"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$category}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="currency_symbol"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="hide_confidential"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="locale_warn"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="locale_alert"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="debug"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="readonly"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>

  {{assign var="category" value="formats"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$category}}{{/tr}}</th>
  </tr>
  
  {{include file=inc_config_date_format.tpl var=date}}
  {{include file=inc_config_date_format.tpl var=time}}
  {{include file=inc_config_date_format.tpl var=longdate}}
  {{include file=inc_config_date_format.tpl var=longtime}}
  {{include file=inc_config_date_format.tpl var=datetime}}
  
  {{assign var="var" value="timezone"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">{{tr}}config-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="{{$var}}">
        {{foreach from=$timezones item=timezone_group key=title_group}}
          <optgroup label="{{$title_group}}">
            {{foreach from=$timezone_group item=title key=timezone}}
              <option value="{{$timezone}}" {{if $timezone==$dPconfig.$var}}selected="selected"{{/if}}>
                {{$title}}
              </option>
            {{/foreach}}
          </optgroup>
        {{/foreach}}
      </select>
    </td>
  </tr>

  {{assign var="var" value="phone_number_format"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" maxlength="14" />
      Le format ne doit contenir que des "9" et des espaces. Il doit y avoir au maximum 10 fois "9". Un "9" correspond à un numéro de 0 à 9.
    </td>
  </tr>

  {{assign var="category" value="system"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$category}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="reverse_proxy"}}
  <tr>
    <th>
      <label for="{{$var}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="mb_id"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="page_title"}}
  <tr>
    <th>
      <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    </td>
  </tr>
	
	{{assign var="var" value="website_url"}}
  <tr>
    <th>
      <label for="{{$var}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" size="30" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>
      
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>

</table>

</form>

<form name="editPHPConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)" id="php-config" style="display: none;">

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
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
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
                  <input type="text" name="php[{{$key}}]" value="{{$value.local_value}}" disabled="disabled" style="opacity: 0.5" />
                  <input type="checkbox" onclick="toggleInput($(this).previous(), this.checked)" {{if array_key_exists($key, $dPconfig.php)}}checked="checked"{{/if}} />
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