<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form"> 
  <tr>
    <th class="category" colspan="100">Systeme de facturation</th>
  </tr>
  <tr>
    {{assign var="var" value="systeme_facturation"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select class="enum list|siemens|t2a" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}">
        <option value="">Aucun</option>
        <option value="siemens" {{if $dPconfig.$m.$var == "siemens"}}selected="selected"{{/if}}>Siemens</option>
        <option value="t2a" {{if $dPconfig.$m.$var == "t2a"}}selected="selected"{{/if}}>T2A</option>
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

{{mb_include template=inc_configure_ghs}}

{{mb_include template=inc_configure_facture_hprim}}

