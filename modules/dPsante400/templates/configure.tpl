<script language="Javascript" type="text/javascript">

function purgeObjects() {
  if (confirm("Merci de confirmer la purge de tous les éléments synchronisés")) {
    var url = new Url;
    url.setModuleAction("dPsante400", "httpreq_purge_objects");
    url.requestUpdate("purgeObjects");
  }
}

</script>

<h2>Environnement d'execution</h2>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <tr>
    <th class="category" colspan="0">Connexion à la source de données</th>
  </tr>
  
  {{assign var="var" value="dsn"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="user"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="pass"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>  
    
  <tr>
    <th class="category" colspan="0">Traitement des mouvements</th>
  </tr>

  {{assign var="var" value="group_id"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="num" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="nb_rows"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="num pos" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="mark_row"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="bool" name="{{$m}}[{{$var}}]">
        <option value="0" {{if $dPconfig.$m.$var == 0}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if $dPconfig.$m.$var == 1}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
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

<h2>Purge des données importés</h2>

<div class="big-warning">
  Attention, cette option permet de purger la base de données de tous les éléments 
  synchronisés dupuis une application tierces, A utiliser avec une extrême prudence, 
  car <strong>l'opération est irréversible</strong> !
</div>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  <tr>
    <td>
      <button class="tick" onclick="purgeObjects()">
        Purger tous les objets syncrhonisés
      </button>
    </td>
    <td class="text" id="purgeObjects" />
  </tr>

</table>