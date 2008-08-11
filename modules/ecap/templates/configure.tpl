{{* $id: $ *}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <th class="category" colspan="6">DHE e-Cap</th>
  </tr>

  {{assign var="mod" value="interop"}}
  {{assign var="var" value="base_url"}}
  <tr>
    <th colspan="3">
      <label for="{{$mod}}[{{$var}}]" title="{{tr}}config-{{$mod}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <input class="str" size="60" name="{{$mod}}[{{$var}}]" value="{{$dPconfig.$mod.$var}}" />
    </td>
  </tr>  

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
