<!-- Variables de configuration -->

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  
  {{assign var="class" value="CPrescription"}}
  {{assign var="var" value="add_element_category"}}
  <tr>
   <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
   </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  {{assign var="var" value="time_print_ordonnance"}}
  <tr>
    <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
      heures
    </td>             
  </tr>
  
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

<hr />

<!-- Imports/Exports -->

<script type="text/javascript">

function startAssociation(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_do_add_table_association");
  url.requestUpdate("do_add_association");
}

function exportElementsPrescription(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_export_elements_prescription");
  url.requestUpdate("export_elements_prescription");
}

</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">Opération d'imports et exports</th>
  </tr>

  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startAssociation()" >Importer la table de gestion de donnees</button></td>
    <td id="do_add_association"></td>
  </tr>

  <tr>
    <td><button class="tick" onclick="exportElementsPrescription()" >Exporter les éléments de prescriptions</button></td>
    <td id="export_elements_prescription"></td>
  </tr>

</table>
