<script type="text/javascript">

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

function addAnt(ant) {
  oTypesAnt = document.editConfig["dPpatients[CAntecedent][types]"];
  if(oTypesAnt.value) {
    oTypesAnt.value = oTypesAnt.value + "|" + ant;
  } else {
    oTypesAnt.value = ant;
  }
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
    </th>
    <td colspan="3">
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" size="80" />
    </td>
  </tr>

  <tr>
    <th>Valeurs possibles</th>
    <td class="text" colspan="3">
      <span onclick="addAnt('med')">{{tr}}CAntecedent.type.med{{/tr}}</span>,
      <span onclick="addAnt('alle')">{{tr}}CAntecedent.type.alle{{/tr}}</span>,
      <span onclick="addAnt('trans')">{{tr}}CAntecedent.type.trans{{/tr}}</span>,
      <span onclick="addAnt('obst')">{{tr}}CAntecedent.type.obst{{/tr}}</span>,
      <span onclick="addAnt('chir')">{{tr}}CAntecedent.type.chir{{/tr}}</span>,
      <span onclick="addAnt('fam')">{{tr}}CAntecedent.type.fam{{/tr}}</span>,
      <span onclick="addAnt('anesth')">{{tr}}CAntecedent.type.anesth{{/tr}}</span>,
      <span onclick="addAnt('gyn')">{{tr}}CAntecedent.type.gyn{{/tr}}</span>,
      <span onclick="addAnt('cardio')">{{tr}}CAntecedent.type.cardio{{/tr}}</span>,
      <span onclick="addAnt('pulm')">{{tr}}CAntecedent.type.pulm{{/tr}}</span>,
      <span onclick="addAnt('stomato')">{{tr}}CAntecedent.type.stomato{{/tr}}</span>,
      <span onclick="addAnt('plast')">{{tr}}CAntecedent.type.plast{{/tr}}</span>,
      <span onclick="addAnt('ophtalmo')">{{tr}}CAntecedent.type.ophtalmo{{/tr}}</span>,
      <span onclick="addAnt('digestif')">{{tr}}CAntecedent.type.digestif{{/tr}}</span>,
      <span onclick="addAnt('gastro')">{{tr}}CAntecedent.type.gastro{{/tr}}</span>,
      <span onclick="addAnt('stomie')">{{tr}}CAntecedent.type.stomie{{/tr}}</span>,
      <span onclick="addAnt('uro')">{{tr}}CAntecedent.type.uro{{/tr}}</span>,
      <span onclick="addAnt('ortho')">{{tr}}CAntecedent.type.ortho{{/tr}}</span>,
      <span onclick="addAnt('traumato')">{{tr}}CAntecedent.type.traumato{{/tr}}</span>,
      <span onclick="addAnt('amput')">{{tr}}CAntecedent.type.amput{{/tr}}</span>,
      <span onclick="addAnt('neurochir')">{{tr}}CAntecedent.type.neurochir{{/tr}}</span>,
      <span onclick="addAnt('greffe')">{{tr}}CAntecedent.type.greffe{{/tr}}</span>,
      <span onclick="addAnt('thrombo')">{{tr}}CAntecedent.type.thrombo{{/tr}}</span>,
      <span onclick="addAnt('cutane')">{{tr}}CAntecedent.type.cutane{{/tr}}</span>,
      <span onclick="addAnt('hemato')">{{tr}}CAntecedent.type.hemato{{/tr}}</span>,
      <span onclick="addAnt('rhumato')">{{tr}}CAntecedent.type.rhumato{{/tr}}</span>,
      <span onclick="addAnt('neuropsy')">{{tr}}CAntecedent.type.neuropsy{{/tr}}</span>,
      <span onclick="addAnt('infect')">{{tr}}CAntecedent.type.infect{{/tr}}</span>,
      <span onclick="addAnt('endocrino')">{{tr}}CAntecedent.type.endocrino{{/tr}}</span>,
      <span onclick="addAnt('carcino')">{{tr}}CAntecedent.type.carcino{{/tr}}</span>,
      <span onclick="addAnt('orl')">{{tr}}CAntecedent.type.orl{{/tr}}</span>
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

