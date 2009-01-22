<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  {{assign var="class" value="CUser"}}
  
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="strong_password"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td>
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>       
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>
  </tr>
 {{assign var="var" value="max_login_attempts"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
    
  </tr>
</table>

</form>


<h2>Actions</h2>

<script type="text/javascript">

function checkSiblings() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("admin", "check_siblings");
  CCAMUrl.requestUpdate("check_siblings");
}

</script>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="checkSiblings()">{{tr}}mod-admin-action-check_siblings{{/tr}}</button></td>
    <td id="check_siblings"></td>
  </tr>
</table>

