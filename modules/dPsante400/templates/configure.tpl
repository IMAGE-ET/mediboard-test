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
  {{assign var="idTr" value="config-dPsante400"}}
  {{assign var="idName" value="dPsante400"}}
  <tr>
    <th class="category" colspan="0">Connexion à la source de données</th>
  </tr>
  
  <tr>
    <th>
      <label for="{{$idName}}[dsn]" title="{{tr}}{{$idTr}}-dsn-desc{{/tr}}">{{tr}}{{$idTr}}-dsn{{/tr}}</label>  
    </th>
    <td>
      <input class="str" name="{{$idName}}[dsn]" value="{{$dPconfig.dPsante400.dsn}}" />
    </td>
  </tr>  
    
  <tr>
    <th>
      <label for="{{$idName}}[user]" title="{{tr}}{{$idTr}}-user-desc{{/tr}}">{{tr}}{{$idTr}}-user{{/tr}}</label>  
    </th>
    <td>
      <input class="str" name="{{$idName}}[user]" value="{{$dPconfig.dPsante400.user}}" />
    </td>
  </tr>  
    
  <tr>
    <th>
      <label for="{{$idName}}[pass]" title="{{tr}}{{$idTr}}-pass-desc{{/tr}}">{{tr}}{{$idTr}}-pass{{/tr}}</label>  
    </th>
    <td>
      <input class="str" name="{{$idName}}[pass]" value="{{$dPconfig.dPsante400.pass}}" />
    </td>
  </tr>  
    
  <tr>
    <th class="category" colspan="0">Traitement des mouvements</th>
  </tr>

  <tr>
    <th>
      <label for="{{$idName}}[group_id]" title="{{tr}}{{$idTr}}-group_id-desc{{/tr}}">{{tr}}{{$idTr}}-group_id{{/tr}}</label>  
    </th>
    <td>
      <input class="num" name="{{$idName}}[group_id]" value="{{$dPconfig.dPsante400.group_id}}" />
    </td>
  </tr>  
    
  <tr>
    <th>
      <label for="{{$idName}}[nb_rows]" title="{{tr}}{{$idTr}}-nb_rows-desc{{/tr}}">{{tr}}{{$idTr}}-nb_rows{{/tr}}</label>  
    </th>
    <td>
      <input class="num pos" name="{{$idName}}[nb_rows]" value="{{$dPconfig.dPsante400.nb_rows}}" />
    </td>
  </tr>  
    
  <tr>
    <th>
      <label for="{{$idName}}[mark_row]" title="{{tr}}{{$idTr}}-mark_row-desc{{/tr}}">{{tr}}{{$idTr}}-mark_row{{/tr}}</label>  
    </th>
    <td>
      <select class="bool" name="{{$idName}}[mark_row]">
        <option value="">&mdash; Choisir</option>
        <option value="0" {{if $dPconfig.dPsante400.mark_row == 0}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if $dPconfig.dPsante400.mark_row == 1}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
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