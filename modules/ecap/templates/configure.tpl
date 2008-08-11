{{* $id: $ *}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Champs RPU -->  
  <tr>
    <th class="category" colspan="100">Mode RPU</th>
  </tr>
  
  <tr>
    {{assign var=var value=old_rpu}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select class="bool" name="{{$m}}[{{$var}}]">
        <option value="0" {{if 0 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if 1 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
