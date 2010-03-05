<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form"> 
    <tr>
      <th class="title" colspan="2">Systeme de facturation</th>
    </tr>
    <tr>
      {{assign var="var" value="systeme_facturation"}}
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td >
        <select class="enum list|siemens|t2a" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}">
          <option value="">Aucun</option>
          <option value="siemens" {{if $dPconfig.$m.$var == "siemens"}}selected="selected"{{/if}}>Siemens</option>
          <option value="t2a" {{if $dPconfig.$m.$var == "t2a"}}selected="selected"{{/if}}>T2A</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<table class="form">  
  <tr>
    <th class="title">
      {{tr}}config-exchange-source{{/tr}}
    </th>
  </tr>
  <tr>
    <td> {{mb_include module=system template=inc_config_exchange_source source=$pmsi_source}} </td>
  </tr>
</table>

{{mb_include template=inc_configure_ghs}}

{{mb_include template=inc_configure_facture_hprim}}

