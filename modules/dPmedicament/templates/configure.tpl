<script type="text/javascript">

function startBCBGES(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_do_add_bcbges");
  url.requestUpdate("bcbges");
}

</script>



<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Niveau d'affichage des produits pour la recherche dans les classes ATC -->  
  <tr>
    <th class="category" colspan="100">Configuration recherche ATC</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CBcbClasseATC"}}
    {{assign var="var" value="niveauATC"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>2</option>
        <option value="3" {{if 3 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>3</option>
        <option value="4" {{if 4 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>4</option>
        <option value="5" {{if 5 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>5</option>
      </select>
    </td>
  </tr>
    
  <tr>
    <th class="category" colspan="100">Configuration recherche BCB</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CBcbClasseTherapeutique"}}
    {{assign var="var" value="niveauBCB"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>2</option>
        <option value="3" {{if 3 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>3</option>
        <option value="4" {{if 4 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>4</option>
        <option value="5" {{if 5 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>5</option>
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

{{include file="../../system/templates/configure_dsn.tpl" dsn=bcb}}
{{include file="../../system/templates/configure_dsn.tpl" dsn=bcbges}}

<h2>Import de la base de données BCBGES</h2>
<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startBCBGES()" >Importer la base de données BCB GESTION</button></td>
    <td id="bcbges"></td>
  </tr>
</table>


