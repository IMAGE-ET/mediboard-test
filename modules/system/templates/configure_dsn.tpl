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

<table class="main"> 
  <tr>
    <td class="">

<form name="ConfigDSN-{{$dsn}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

<!-- Configure dsn '{{$dsn}}' -->
{{assign var="section" value="db"}}

<tr>
  <th class="title" colspan="100">
    {{tr}}config-{{$section}}{{/tr}} '{{$dsn}}'
    {{assign var=dsnConfig value=0}}
    {{if $dsn|array_key_exists:$dPconfig.$section}}
    {{assign var=dsnConfig value=$dPconfig.$section.$dsn}}
    {{/if}} 
  </th>
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
    <select class="str" name="{{$section}}[{{$dsn}}][{{$var}}]">
      <option value="mysql"  {{if "mysql"  == $value}} selected="selected" {{/if}}>{{tr}}config-{{$section}}-{{$var}}-mysql{{/tr}}</option>
      <option value="ingres" {{if "ingres" == $value}} selected="selected" {{/if}}>{{tr}}config-{{$section}}-{{$var}}-ingres{{/tr}}</option>
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
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
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
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
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
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
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
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  <td class="button" colspan="2">
    {{mb_ternary test=$dsnConfig var=button_text value=Modify other=Create}}
    {{mb_ternary test=$dsnConfig var=button_class value=modify other=new}}
    <button class="{{$button_class}}" type="submit">{{tr}}{{$button_text}}{{/tr}}</button>
  </td>
</tr>

</table>

</form>

    </td>
		<td class="greedyPane">

<script type="text/javascript">

var DSN = {
  create: function (sDSN) {
    var oForm = document.forms["CreateDSN-" + sDSN];
    
    var url = new Url;
    url.setModuleAction("system", "httpreq_create_dsn");
    url.addParam("dsn", sDSN);
    url.addElement(oForm.master_user);
    url.addElement(oForm.master_pass);
    url.requestUpdate("config-dsn-create-" + sDSN);
  },
  test: function (sDSN) {
    var url = new Url;
    url.setModuleAction("system", "httpreq_test_dsn");
    url.addParam("dsn", sDSN);
    url.requestUpdate("config-dsn-test-" + sDSN);
  }
}

</script>

<table class="tbl">

<tr>
  <th class="title" colspan="100">
    {{tr}}config-admin-dsn{{/tr}} '{{$dsn}}'
  </th>
</tr>

<!-- Test DSN -->
<tr>
  <td>
    <button type="button" class="search" onclick="DSN.test('{{$dsn}}');">
      {{tr}}config-dsn-test{{/tr}}
    </button>
  </td>
  <td id="config-dsn-test-{{$dsn}}" />
</tr>

<!-- Create DSN -->
<tr>
  <td>
    <form name="CreateDSN-{{$dsn}}" action="?" method="get">
      <label for="master_user">{{tr}}CreateDSN-master_user{{/tr}}</label>
      <input name="master_user" type="text" />
      <br/>
      <label for="master_pass">{{tr}}CreateDSN-master_pass{{/tr}}</label>
      <input name="master_pass" type="password" />
      <br/>
      <button type="button" class="modify" onclick="DSN.create('{{$dsn}}');">
        {{tr}}config-dsn-create{{/tr}}
      </button>
    </form>
  </td>
  <td id="config-dsn-create-{{$dsn}}" />
</tr>

</table>

		</td>
	</tr>
</table>