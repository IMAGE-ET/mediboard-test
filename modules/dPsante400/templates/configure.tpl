<script type="text/javascript">

function purgeObjects() {
  if (confirm("Merci de confirmer la purge de tous les éléments synchronisés")) {
    var url = new Url;
    url.setModuleAction("dPsante400", "httpreq_purge_objects");
    url.requestUpdate("purgeObjects");
  }
}

var Moves = {
  doAction: function(sAction) {
    if (sAction == "purge") {
      if (!confirm("Merci de confirmer la purge de tous les mouvements")) {
        return;
      }
    }

    var url = new Url;
    url.setModuleAction("dPsante400", "httpreq_do_moves");
    url.addParam("action", sAction);
    url.addElement($("PurgeType"));
    url.addElement($("PurgeMarked"));
    url.requestUpdate("purgeMoves");
  },
}

</script>

<h2>Environnement d'execution</h2>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <tr>
    <th class="category" colspan="100">Connexion à la source de données</th>
  </tr>
  
  {{assign var="mod" value="interop"}}
  {{assign var="var" value="mode_compat"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$var}}]" title="{{tr}}config-{{$mod}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$var}}{{/tr}}
    </th>
    <td>
      <select name="{{$mod}}[{{$var}}]">
        <option value="default" {{if $dPconfig.$mod.$var == "default"}}selected="selected"{{/if}}>Par défaut</option>
        <option value="medicap" {{if $dPconfig.$mod.$var == "medicap"}}selected="selected"{{/if}}>Medicap</option>
        <option value="tonkin"  {{if $dPconfig.$mod.$var == "tonkin"}}selected="selected"{{/if}}>Tonkin</option>
      </select>
    </td>
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
    <th class="category" colspan="100">Traitement des mouvements</th>
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
    <th class="category" colspan="100">Synchronisation des objets</th>
  </tr>

  {{assign var="var" value="cache_hours"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="num min|0" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>  

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<table class="tbl">
  <tr>
    <th>Choix des mouvements</th>
    <th>Action sur les mouvements</th>
    <th>Status</th>
  </tr>
  <tr>
    <td>
      <label for="PurgeType" title="{{tr}}CMouvement400-type-desc{{/tr}}">{{tr}}CMouvement400-type{{/tr}}</label>
      <select id="PurgeType" name="type">
        {{foreach from=$types item=_type}}        
        <option value="{{$_type}}">{{tr}}CMouvement400-type-{{$_type}}{{/tr}}</option>
        {{foreachelse}}
        <option value="">Pas de type disponible</option>
        {{/foreach}}
      </select>
      
      <br />
      
	    <label for="marked" title="{{tr}}CMouvement400-marked-desc{{/tr}}">{{tr}}CMouvement400-marked{{/tr}}</label>
	    <select id="PurgeMarked" name="marked">
	      <option value="0">{{tr}}CMouvement400-marked-0{{/tr}}</option>
	      <option value="1">{{tr}}CMouvement400-marked-1{{/tr}}</option>
	    </select>
    </td>
    <td>
      <button class="search" onclick="Moves.doAction('count')">
        Compter
      </button>
      <button class="trash" onclick="Moves.doAction('purge')">
        Purger
      </button>
    </td>
    <td class="text" id="purgeMoves" />
  </tr>

</table>

<h2>Purge des mouvements non traités</h2>

<div class="big-warning">
  Attention, cette option permet de purger la base de données tierce de tous les
  mouvements non traités par Mediboard. une application tierces.
  <br />
  A utiliser avec une extrême prudence, car <strong>l'opération est irréversible</strong> !
</div>

<h2>Purge des données importés</h2>

<div class="big-warning">
  Attention, cette option permet de purger la base de données de Mediboard de tous 
  les éléments synchronisés dupuis une application tierces. 
  <br /> A utiliser avec une extrême prudence, car <strong>l'opération est irréversible</strong> !
</div>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  <tr>
    <td>
      <button class="trash" onclick="purgeObjects()">
        Purger tous les objets synchronisés
      </button>
    </td>
    <td class="text" id="purgeObjects" />
  </tr>

</table>
