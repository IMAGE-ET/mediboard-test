{{assign var=class value=CAntecedent}}

<script type="text/javascript">
var oTokenTypes = null;
var oTokenAppareils   = null;
Main.add(function () {
  var form = getForm("EditConfig-{{$class}}");
	var fieldTypes     = form["dPpatients[CAntecedent][types]"    ];
  var fieldAppareils = form["dPpatients[CAntecedent][appareils]"];
  oTokenTypes     = new TokenField(fieldTypes   );
  oTokenAppareils = new TokenField(fieldAppareils);
});

</script>

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  <tr>
    {{assign var="var" value="types"}}
    <th class="category halfPane" colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}" 
        onclick="$(this.htmlFor).toggle(); $$('div.non-type').invoke('toggle');"
      >
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
    </th>
    {{assign var="var" value="appareils"}}
    <th class="category halfPane" colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}" 
        onclick="$(this.htmlFor).toggle();"
      >
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
    </th>
  </tr>
  
  <tr>
    <td class="text" colspan="3">
      {{assign var="var" value="types"}}
      <input type="text" style="display: none" name="{{$m}}[{{$class}}][{{$var}}]" size="80" value="{{$conf.$m.$class.$var}}" />
      
      {{assign var=static_types     value="CAntecedent"|static:types}}
      {{assign var=static_non_types value="CAntecedent"|static:non_types}}

      {{foreach from=$all_types item=_type}}
        {{if in_array($_type, $static_non_types)}}
        <div class="non-type opacity-50" style="width: 16em; float: left; display: none;">
        {{else}}
        <div style="width: 16em; float: left;">
        {{/if}}
          <label>
            <input type="checkbox" name="types_antecedents[]" value="{{$_type}}" 
              onchange="oTokenTypes.toggle('{{$_type}}', this.checked)"
              {{if in_array($_type, $active_types)}}checked="checked"{{/if}} 
            /> 
            {{if !in_array($_type, $static_types)}}<strong>Ex:</strong>{{/if}} 
            {{tr}}CAntecedent.type.{{$_type}}{{/tr}}
          </label>
        </div>
      {{/foreach}}
    </td>
    
    <td class="text" colspan="3">
      {{assign var="var" value="appareils"}}
      <input type="text" style="display: none" name="{{$m}}[{{$class}}][{{$var}}]" size="80" value="{{$conf.$m.$class.$var}}" />
  
      {{assign var=static_appareils value="CAntecedent"|static:appareils}}

      {{foreach from=$all_appareils item=_appareil}}
      <div style="width: 16em; float: left;">
        <label>
          <input type="checkbox" name="appareils_antecedents[]" value="{{$_appareil}}" 
            onchange="oTokenAppareils.toggle('{{$_appareil}}', this.checked)"
            {{if in_array($_appareil, $active_appareils)}}checked="checked"{{/if}} 
          /> 
            {{if !in_array($_appareil, $static_appareils)}}<strong>Ex:</strong>{{/if}} 
          {{tr}}CAntecedent.appareil.{{$_appareil}}{{/tr}}
        </label>
      </div>
      {{/foreach}}
    </td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
