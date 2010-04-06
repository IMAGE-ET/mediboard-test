{{assign var=class value=CDossierMedical}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />
    
  {{mb_include module=system template=inc_config_bool var=diags_static_cim}}
  
  {{assign var=class value=CConstantesMedicales}}
  {{assign var=var value=important_constantes}}
  
  {{assign var=field  value="$m[$class][$var]"}}
  {{assign var=value  value=$dPconfig.$m.$class.$var}}
  {{assign var=locale value=config-$m-$class-$var}}
  <tr>
    <th>
      <label for="{{$field}}" title="{{tr}}{{$locale}}-desc{{/tr}}">
        {{tr}}{{$locale}}{{/tr}}
      </label>  
    </th>
  
    <td>
      <script type="text/javascript">
        var tokenField;
        Main.add(function(){
          tokenField = new TokenField(getForm("EditConfig-CDossierMedical")["{{$field}}"]);
        });
      </script>
      
      <input type="hidden" value="{{$value}}" name="{{$field}}" />
      
      {{assign var=selection value="|"|explode:$value}}
      
      {{foreach from=$class|static:list_constantes key=_key item=_params}}
        <label>
          <input type="checkbox" onclick="tokenField.toggle(this.value, this.checked)" value="{{$_key}}" {{if in_array($_key, $selection)}}checked="checked"{{/if}} /> {{tr}}{{$class}}-{{$_key}}{{/tr}}
        </label><br />
      {{/foreach}}
    </td>
  </tr>

  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
