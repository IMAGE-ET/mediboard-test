<script type="text/javascript">

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

function activateAntecedent(ant, active) {
  oTypesAnt = document.editConfig["dPpatients[CAntecedent][types]"];
  aTypesAnt = oTypesAnt.value.split('|').without('');
  if(active && aTypesAnt.indexOf(ant) == -1) {
    aTypesAnt.push(ant);
  }
  else {
    aTypesAnt = aTypesAnt.without(ant);
  }
  oTypesAnt.value = aTypesAnt.join('|');
}

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

  {{assign var="var" value="date_naissance"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
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
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
      <input type="hidden" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
      
    </th>
    <td class="text" colspan="3">
    <table>
      <tr>
      {{foreach from=$types_antecedents item=ant name=list_antecedents}}
        <td>
          <label>
            <input type="checkbox" name="types_antecedents[]" value="{{$ant}}" 
            {{if in_array($ant, $types_antecedents_active)}}checked="checked"{{/if}} 
            onchange="activateAntecedent('{{$ant}}', this.checked)"
            /> 
            {{tr}}CAntecedent.type.{{$ant}}{{/tr}}
          </label>
        </td>
        {{if $smarty.foreach.list_antecedents.index % 4 == 3}}</tr><tr>{{/if}}
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

