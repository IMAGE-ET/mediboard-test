<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="class" value="CBloodSalvage"}}
    {{assign var="var" value="inLivretTherapeutique"}}
    
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  <tr>
    <th style="width:50%">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
    </th>
    <td>
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>