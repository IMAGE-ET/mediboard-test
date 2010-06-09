{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{* 
 @param string $dsn
*}}

<script type="text/javascript">

var DSN = {
  create: function (sDSN) {
    var oForm = document.forms["CreateDSN-" + sDSN];
    
    var url = new Url("system", "httpreq_create_dsn");
    url.addParam("dsn", sDSN);
    url.addElement(oForm.master_user);
    url.addElement(oForm.master_pass);
    url.requestUpdate("config-dsn-create-" + sDSN);
  },
  test: function (sDSN) {
    var url = new Url("system", "httpreq_test_dsn");
    url.addParam("dsn", sDSN);
    url.requestUpdate("config-dsn-test-" + sDSN);
  }
};

</script>

<!-- Configure dsn '{{$dsn}}' -->
{{assign var=section value="db"}}
{{assign var=dsnConfig value=0}}

{{if $dsn|array_key_exists:$dPconfig.$section}}
  {{assign var=dsnConfig value=$dPconfig.$section.$dsn}}
{{/if}} 

<table class="main"> 
  <tr>
    <th colspan="2" class="title">{{tr}}config-{{$section}}{{/tr}} '{{$dsn}}'</th>
  </tr>
  <tr>
    <td style="width: 30%;">

<form name="ConfigDSN-{{$dsn}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  <col style="width: 30%" />


<tr>
  <th class="category" colspan="2">{{tr}}config-{{$section}}-connection{{/tr}}</th>
</tr>

<tr>
  {{assign var="var" value="dbtype"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{mb_ternary test=$dsnConfig var=value value=$dsnConfig.$var other=""}}
    <select name="{{$section}}[{{$dsn}}][{{$var}}]">
      {{foreach from="CSQLDataSource"|static:engines key=engine item=class}}
        <option value="{{$engine}}" {{if $engine == $value}}selected="selected"{{/if}}>{{tr}}config-{{$section}}-{{$var}}-{{$engine}}{{/tr}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  {{assign var="var" value="dbhost"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{mb_ternary test=$dsnConfig var=value value=$dsnConfig.$var other=""}}
    <input type="text" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  {{assign var="var" value="dbname"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{mb_ternary test=$dsnConfig var=value value=$dsnConfig.$var other=""}}
    <input type="text" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  {{assign var="var" value="dbuser"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{mb_ternary test=$dsnConfig var=value value=$dsnConfig.$var other=""}}
    <input type="text" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  {{assign var="var" value="dbpass"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{mb_ternary test=$dsnConfig var=value value=$dsnConfig.$var other=""}}
    <input type="password" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  <th>
    <button type="button" class="search" onclick="DSN.test('{{$dsn}}');">
      {{tr}}config-dsn-test{{/tr}}
    </button>
  </th>
  <td id="config-dsn-test-{{$dsn}}"></td>
</tr>

<tr>
  <td class="button" colspan="2">
    <button class="{{$dsnConfig|@ternary:modify:new}}" type="submit">{{tr}}{{$dsnConfig|@ternary:Save:Create}}{{/tr}}</button>
  </td>
</tr>

</table>

</form>

    </td>
		<td>

<form name="CreateDSN-{{$dsn}}" action="?" method="get">

  <table class="form">
    <col style="width: 30%" />
    
    <tr>
      <th class="category" colspan="2">{{tr}}config-admin-dsn{{/tr}}</th>
    </tr>
    
    <!-- Create DSN -->
    <tr>
      <th><label for="master_user">{{tr}}CreateDSN-master_user{{/tr}}</label></th>
      <td><input name="master_user" type="text" /></td>
    </tr>
    <tr>
      <th><label for="master_pass">{{tr}}CreateDSN-master_pass{{/tr}}</label></th>
      <td><input name="master_pass" type="password" /></td>
    </tr>
    <tr>
      <th>
        <button type="button" class="modify" onclick="DSN.create('{{$dsn}}');">
          {{tr}}config-dsn-create{{/tr}}
        </button>
      </th>
      <td id="config-dsn-create-{{$dsn}}"></td>
    </tr>
  </table>

</form>

		</td>
	</tr>
</table>