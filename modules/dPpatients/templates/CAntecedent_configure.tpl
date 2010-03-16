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
    <th class="category" colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
      <input type="hidden" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" size="100" />
    </th>
    {{assign var="var" value="appareils"}}
    <th class="category" colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
      <input type="hidden" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" size="100" />
    </th>
  </tr>
  <tr>
    <td class="text" colspan="3">
	    <table>
	      <tr>
	      {{foreach from=$types_antecedents item=ant name=list_antecedents}}
	        <td>
	          <label>
	            <input type="checkbox" name="types_antecedents[]" value="{{$ant}}" 
	            {{if in_array($ant, $types_antecedents_active)}}checked="checked"{{/if}} 
	            onchange="oTokenTypes.toggle('{{$ant}}', this.checked)"
	            /> 
	            {{tr}}CAntecedent.type.{{$ant}}{{/tr}}
	          </label>
	        </td>
	        {{if $smarty.foreach.list_antecedents.index % 4 == 3}}</tr><tr>{{/if}}
	      {{/foreach}}
	      </tr>
	    </table>
    </td>
    <td class="text" colspan="3">
	    <table>
	      <tr>
	      {{foreach from=$appareils_antecedents item=appareil name=list_appareils}}
	        <td>
	          <label>
	            <input type="checkbox" name="appareils_antecedents[]" value="{{$appareil}}" 
	            {{if in_array($appareil, $appareils_antecedents_active)}}checked="checked"{{/if}} 
	            onchange="oTokenAppareils.toggle('{{$appareil}}', this.checked)"
	            /> 
	            {{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}}
	          </label>
	        </td>
	        {{if $smarty.foreach.list_appareils.index % 4 == 3}}</tr><tr>{{/if}}
	      {{/foreach}}
	      </tr>
	    </table>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
