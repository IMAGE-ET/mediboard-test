{{assign var=class value=CPatient}}
<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{mb_include module=system template=inc_config_str var=tag_ipp      }}
  {{mb_include module=system template=inc_config_str var=tag_ipp_trash}}
  
  {{assign var="var" value="identitovigilence"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td class="text">
      <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="nodate"   {{if $dPconfig.$m.$class.$var == "nodate"  }} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-nodate{{/tr}}</option>
        <option value="date"     {{if $dPconfig.$m.$class.$var == "date"    }} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-date{{/tr}}</option>
        <option value="doublons" {{if $dPconfig.$m.$class.$var == "doublons"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-doublons{{/tr}}</option>
      </select> 
    </td>            
  </tr>
  
  {{assign var="var" value="multi_group"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td class="text">
      <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="full"    {{if $dPconfig.$m.$class.$var == "full"   }} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-full{{/tr}}</option>
        <option value="limited" {{if $dPconfig.$m.$class.$var == "limited"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-limited{{/tr}}</option>
        <option value="hidden"  {{if $dPconfig.$m.$class.$var == "hidden" }} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-hidden{{/tr}}</option>
      </select> 
    </td>            
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=merge_only_admin}}
  {{mb_include module=system template=inc_config_bool var=extended_print  }}
  {{mb_include module=system template=inc_config_str  var=adult_age       }}

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>