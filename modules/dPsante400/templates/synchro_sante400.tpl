<script type="text/javascript">
function retry(iRec) {
  var url = new Url("{{$m}}", "{{$action}}");
  url.addParam("rec", iRec);
  url.addParam("verbose", 1);
  url.popup(900, 700, "Explaination Import Sante400");
}
  
</script>
  
{{if !$connection}}
<div class="big-error">
Impossible d'établir la connexion avec le serveur Santé400<br/>
Merci de vérifier les paramètres de la configuration ODBC pour la source 'sante400'
</div>
{{/if}}

<table class="main">

{{if !$dialog}}
<tr>
  <td style="text-align: left">

    <form action="?" name="typeFilter" method="get">

    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

    <label for="type" title="{{tr}}CMouvement400-type-desc{{/tr}}">{{tr}}CMouvement400-type{{/tr}}</label>
    <select name="type" onchange="this.form.submit()">
      {{foreach from=$types item=_type}}
      <option value="{{$_type}}" {{if $_type == $type}}selected="selected"{{/if}}>{{tr}}CMouvement400-type-{{$_type}}{{/tr}}</option>
      {{/foreach}}
    </select>

    </form>
  
  </td>

  <td style="text-align: right">

    <form action="?" name="markFilter" method="get">

    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

    <label for="marked" title="{{tr}}CMouvement400-marked-desc{{/tr}}">{{tr}}CMouvement400-marked{{/tr}}</label>
    <select name="marked" onchange="this.form.submit()">
      <option value="0" {{if !$marked}}selected="selected"{{/if}}>{{tr}}CMouvement400-marked-0{{/tr}}</option>
      <option value="1" {{if  $marked}}selected="selected"{{/if}}>{{tr}}CMouvement400-marked-1{{/tr}}</option>
    </select>

    </form>
  
  </td>
</tr>
{{/if}}

<tr>
  <td colspan="2">

<table class="tbl">

<tr>
  <th class="title" colspan="100">
  	Imports de {{$mouvs|@count}} {{tr}}CMouvement400-type-{{$type}}{{/tr}}
  	sur {{$count}} disponibles
  	{{if $dPconfig.dPsante400.group_id}}
  	<br />Filtré sur l'établissement '{{$dPconfig.dPsante400.group_id}}'
  	{{/if}}
  </th>
</tr>

<tr>
  <th colspan="3">Santé 400</th>
  <th colspan="20">Import Mediboard</th>
</tr>

<tr>
  <th>Numéro</th>
  <th>Quand</th>
  <th>Type</th>
  <th>Marque</th>
  <th>Etablissement</th>
  <th>Cabinet <br /> Salle <br /> Service</th>
  <th>Praticien</th>
  <th>Patient</th>
  <th>Sejour</th>
  <th>Intervention</th>
  <th>Actes</th>
  <th>Naissance</th>
  <th>Marque</th>

  {{if !$dialog}}
  <th>Détails</th>
  {{/if}}

</tr>

{{foreach from=$mouvs item=curr_mouv}}
<tr>
  <td>{{$curr_mouv->rec}}</td>
  <td>{{$curr_mouv->when}}</td>
  <td class="text">
    {{if $curr_mouv->type == "M"}}
    {{foreach from=$curr_mouv->changedFields item=_field}}
    {{$_field}}
    {{/foreach}}
    {{else}}
    {{$curr_mouv->type}}
    {{/if}}</td>
  <td>{{$curr_mouv->mark}}</td>
  {{foreach from=$curr_mouv->statuses key="index" item="status"}}
  {{assign var="cache" value=$curr_mouv->cached[$index]}}
  <td>
    {{if $status === null}}
    <div class="warning">Failed</div>
    {{elseif $status === "*"}}
    <div class="message">Skipped</div>
    {{else}}
    <div class="message">
      synch:&nbsp;{{$status}}
      {{if $cache}}
      <br />Cache:&nbsp;{{$cache}}
      {{/if}}
    </div>
    {{/if}}
  </td>
  {{/foreach}}
  <td>{{$curr_mouv->status}}</td>

  {{if !$dialog}}
  <td>
    <button class="search" onclick="retry({{$curr_mouv->rec}})">
      {{tr}}Retry{{/tr}}
    </button>
  </td>
  {{/if}}

</tr>
{{/foreach}}

</table>

  </td>
</tr>

</table>