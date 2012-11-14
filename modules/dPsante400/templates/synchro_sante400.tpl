{{mb_script module=sante400 script=mouvements}}

<script type="text/javascript">
Main.add(function() {
  Mouvements.relaunch.curry().delay(5);
})
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

    <input name="relaunch" type="checkbox" {{if $relaunch}} checked="checked" {{/if}} onclick="Mouvements.relaunch();" />
    <label for="relaunch" title="{{tr}}CMouvement400-relaunch-desc{{/tr}}">
      {{tr}}CMouvement400-relaunch{{/tr}}
    </label>

    </form>
  
  </td>

  <td style="text-align: right">

    <form action="?" name="markFilter" method="get">

    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

    <label for="marked" title="{{tr}}CMouvement400-marked-desc{{/tr}}">{{tr}}CMouvement400-marked{{/tr}}</label>
    <select name="marked" onchange="this.form.submit()">
      <option value="0" {{if !$marked}} selected="selected"{{/if}}>{{tr}}CMouvement400-marked-0{{/tr}}</option>
      <option value="1" {{if  $marked}} selected="selected"{{/if}}>{{tr}}CMouvement400-marked-1{{/tr}}</option>
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
  	{{if $conf.dPsante400.group_id}}
  	<br />Filtré sur l'établissement '{{$conf.dPsante400.group_id}}'
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

{{foreach from=$mouvs item=_mouv}}
<tr>
  <td>{{$_mouv->rec}}</td>
  <td>{{$_mouv->when}}</td>
  <td class="text">
    {{if $_mouv->type == "M"}}
    {{foreach from=$_mouv->changedFields item=_field}}
    {{$_field}}
    {{/foreach}}
    {{else}}
    {{$_mouv->type}}
    {{/if}}</td>
  <td>{{$_mouv->mark}}</td>
  {{foreach from=$_mouv->statuses key="index" item="status"}}
  {{assign var="cache" value=$_mouv->cached[$index]}}
  <td>
    {{if $status === null}}
    <div class="warning">Failed</div>
    {{elseif $status === "*"}}
    <div class="info">Skipped</div>
    {{else}}
    <div class="info">
      synch:&nbsp;{{$status}}
      {{if $cache}}
      <br />Cache:&nbsp;{{$cache}}
      {{/if}}
    </div>
    {{/if}}
  </td>
  {{/foreach}}
  <td>{{$_mouv->status}}</td>

  {{if !$dialog}}
  <td>
    <button class="search" onclick="Mouvements.retry('{{$_mouv->class }}', '{{$_mouv->rec}}')">
      {{tr}}Retry{{/tr}}
    </button>
  </td>
  {{/if}}

</tr>
{{foreachelse}}
<tr>
  <td class="empty" colspan="20"> 
    {{tr}}CMouvement400.none{{/tr}}
  </td>
</tr>
{{/foreach}}

</table>

  </td>
</tr>

</table>