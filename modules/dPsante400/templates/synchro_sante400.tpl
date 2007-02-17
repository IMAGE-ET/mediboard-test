<script type="text/javascript">
function explain(iRec) {
  url = new Url();
  url.setModuleAction("{{$m}}", "{{$action}}");
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
  <td style="text-align: right">

    <form action="?" name="markFilter" method="get">

    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

    <label for="marked" title="Types de mouvements">Type de mouvements</label>
    <select name="marked" onchange="this.form.submit()">
      <option value="0" {{if !$marked}}selected="selected"{{/if}}>Mouvements à traiter</option>
      <option value="1" {{if  $marked}}selected="selected"{{/if}}>Mouvement traités avec un erreur</option>
    </select>

    </form>
  
  </td>
</tr>
{{/if}}

<tr>
  <td>

<table class="tbl">

<tr>
  <th class="title" colspan="20">
  	Imports de {{$mouvs|@count}} mouvements de séjours
  	sur {{$count}} disponibles</th>
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
  <th>Cabinet</th>
  <th>Chirurgien</th>
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
  <td>{{$curr_mouv->type}}</td>
  <td>{{$curr_mouv->prod}}</td>
  {{foreach from=$curr_mouv->statuses key="index" item="status"}}
  {{assign var="cache" value=$curr_mouv->cached[$index]}}
  <td>
    {{if $status !== null}}
    <div class="message">
      synchro:&nbsp;{{$status}}
      {{if $cache}}
      <br />cache:&nbsp;{{$cache}}
      {{/if}}
    </div>
    {{else}}
    <div class="warning">Echec</div>
    {{/if}}
  </td>
  {{/foreach}}
  <td>{{$curr_mouv->status}}</td>

  {{if !$dialog}}
  <td>
    <button class="search" onclick="explain({{$curr_mouv->rec}})">Explications</button>
  </td>
  {{/if}}

</tr>
{{/foreach}}

</table>

  </td>
</tr>

</table>