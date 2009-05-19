<script type="text/javascript">

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

var oTokenAntecedents = null;
var oTokenAppareils = null;
Main.add(function () {
  var oField = getForm("editConfig")["dPpatients[CAntecedent][types]"];
  oTokenAntecedents = new TokenField(oField);
  
  var oFieldAppareil = getForm("editConfig")["dPpatients[CAntecedent][appareils]"];
  oTokenAppareils = new TokenField(oFieldAppareil);
  
});

</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{assign var="class" value="CPatient"}}
  <tr>
    <th class="category" colspan="100">Configuration pour les patients</th>
  </tr>
  <tr>
    {{assign var="var" value="tag_ipp"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="identitovigilence"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td class="text" colspan="3">
      <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="nodate" {{if "$dPconfig.$m.$class.$var == nodate"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-nodate{{/tr}}</option>
        <option value="date" {{if $dPconfig.$m.$class.$var == "date"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-date{{/tr}}</option>
        <option value="doublons" {{if $dPconfig.$m.$class.$var == "doublons"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-doublons{{/tr}}</option>
      </select> 
    </td>            
  </tr>
  
  <!-- Merge only for admin -->
  {{assign var="var" value="merge_only_admin"}}
  <tr>
   <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
   </th>
    <td  colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  {{assign var="class" value="intermax"}}
  <tr>
    <th class="category" colspan="100">Configuration Intermax</th>
  </tr>
  <tr>
    {{assign var="var" value="auto_watch"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td  colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}bool.1{{/tr}}</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}bool.0{{/tr}}</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>


  {{assign var="class" value="CAntecedent"}}
  <tr>
    <th class="category" colspan="100">{{tr}}CAntecedent{{/tr}}</th>
  </tr>
  
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
	            onchange="oTokenAntecedents.toggle('{{$ant}}', this.checked)"
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
  
  {{assign var="class" value="CTraitement"}}
  <tr>
    <th class="category" colspan="100">{{tr}}CTraitement{{/tr}}</th>
  </tr>

  <tr>
    {{assign var="var" value="enabled"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td  colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}bool.1{{/tr}}</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}bool.0{{/tr}}</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>

  {{assign var="class" value="CDossierMedical"}}
  <tr>
    <th class="category" colspan="100">{{tr}}CDossierMedical{{/tr}}</th>
  </tr>

  <tr>
    {{assign var="var" value="diags_static_cim"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
    </th>
    <td  colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}bool.1{{/tr}}</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">{{tr}}bool.0{{/tr}}</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
  
</table>
</form>

<h2>Import de la base de données des codes INSEE / ISO</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=INSEE}}

<script type="text/javascript">

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

</script>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Status</th>
</tr>
  
<tr>
  <td>
    <button class="tick" onclick="startINSEE()">
      Importer les codes INSEE / ISO
    </button>
  </td>
  <td id="INSEE" />
</tr>

</table>

{{include file=inc_configure_medecins.tpl}}

