<script type="text/javascript">
function explain(iRec) {
  url = new Url();
  url.setModuleAction("{{$m}}", "{{$action}}");
  url.addParam("rec", iRec);
  url.addParam("verbose", 1);
  url.popup(800, 500, "Explaination Import Sante400");
}
  
</script>
  

<table class="main">

<tr>

<td>

<table class="tbl">

<tr>
  <th class="title" colspan="11">
  	Imports de {{$mouvs|@count}} mouvements de séjours
  	sur {{$count}} disponibles</th>
</tr>

<tr>
  <th colspan="3">Santé 400</th>
  <th colspan="8">Import Mediboard</th>
</tr>

<tr>
  <th>Numéro</th>
  <th>Type</th>
  <th>Marque</th>
  <th>Etablissement</th>
  <th>Cabinet</th>
  <th>Chirurgien</th>
  <th>Patient</th>
  <th>Sejour</th>
  <th>Naissance</th>
  <th>Marque</th>

  {{if !$dialog}}
  <th>Détails</th>
  {{/if}}

</tr>

{{foreach from=$mouvs item=curr_mouv}}
<tr>	
  <td>{{$curr_mouv->rec}}</td>
  <td>{{$curr_mouv->data.CODACT}}</td>
  <td>{{$curr_mouv->data.RETPRODST}}</td>
  <td>{{if @$curr_mouv->status.1 == "E"}}<div class="message">Importé</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.2 == "F"}}<div class="message">Importé</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.3 == "C"}}<div class="message">Importé</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.4 == "P"}}<div class="message">Importé</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.5 == "S"}}<div class="message">Importé</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.6 == "N"}}<div class="message">Importé</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{$curr_mouv->status}}</td>

  {{if !$dialog}}
  <td>
    {{if @$curr_mouv->status.6 != "N"}}
    <button class="search" onclick="explain({{$curr_mouv->data.IDUENR}})">Explications</button>
    {{/if}}  
  </td>
  {{/if}}

</tr>
{{/foreach}}

</table>

</td>

</tr>

</table>