<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  {{assign var="class" value="system"}}
  <tr>
    <th class="category" colspan="6">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
  
    {{assign var="var" value="type_telephone"}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select name="{{$m}}[{{$var}}]">
        <option value="france" {{if $dPconfig.$m.$var == "france"}} selected="selected" {{/if}}>France</option>
        <option value="suisse" {{if $dPconfig.$m.$var == "suisse"}} selected="selected" {{/if}}>Suisse</option>
      </select>
    </td>
  </tr>
    
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>