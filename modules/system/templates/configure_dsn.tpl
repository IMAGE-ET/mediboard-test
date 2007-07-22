{{* $Id: $
 @param string $dsn
*}}

<form name="ConfigDSN-{{$dsn}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

<!-- Configure dsn '{{$dsn}}' -->
{{assign var="section" value="db"}}

<tr>
  <th class="category" colspan="100">
    {{tr}}config-{{$section}}{{/tr}} '{{$dsn}}'
  </th>
</tr>

<tr>
  {{assign var="var" value="dbhost"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
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
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
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
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
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
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  <td class="button" colspan="2">
    <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
  </td>
</tr>

</table>

<script type="text/javascript">

var DSN = {
  create: function (sDSN) {
    var url = new Url;
    url.setModuleAction("system", "test");
  },
  test: function (sDSN) {
    
  }
}
</script>

</form>

<table class="tbl">

<tr>
  <th class="title" colspan="100">
    {{tr}}config-admin-dsn{{/tr}} '{{$dsn}}'
  </th>
</tr>

<!-- Test DSN -->
<tr>
  <td>
    <button class="search" onclick="DSN.test('{{$dsn}}');">
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
      <button class="modify" onclick="submitFormAjax(this, 'config-dsn-create-{{$dsn}}')">
        {{tr}}config-dsn-create{{/tr}}
      </button>
    </form>
  </td>
  <td id="config-dsn-create-{{$dsn}}" />
</tr>

</table>