{{if $verbose}}
<table class="form">
  <tr>
    <th class="category" colspan="2">Bilan</th>
  </tr>

  <tr>
    <th>Etape #</th>
	 	<td>{{$step}}</td>
  </tr>
	<tr>
    <th>M�decins</th>
	 	<td>{{$medecins|@count}}</td>
  </tr>
	<tr>
    <th>Temps pris</th>
	 	<td>{{$chrono->total}}</td>
  </tr>
	<tr>
    <th>Mise � jours</th>
	 	<td>{{$updates}}</td>
  </tr>
	<tr>
    <th>Erreurs</th>
	 	<td>{{$errors}}</td>
  </tr>
</table>
{{/if}}

{{if !$verbose}}
<script type="text/javascript">
{{if $xpath_screwed}}
  Process.updateScrewed();
{{else}}
  Process.updateTotal(
    {{$medecins|@count}},
    {{$chrono->total}},
    {{$updates}},
    {{$errors}}
  );
{{/if}}
{{if count($medecins)}}
  Process.doStep();
{{else}}
	Process.stop();
{{/if}}
</script>
{{/if}}

{{if $verbose}}
<table class="tbl">
  <tr>
  	<th>Nom</th>
    <th>Pr�nom</th>
    <th>Nom de jeune fille</th>
  	<th>Adresse</th>
  	<th>Ville</th>
  	<th>CP</th>
  	<th>T�l</th>
  	<th>Fax</th>
  	<th>M�l</th>
    <th>Disciplines</th>
    <th>Compl�mentaires</th>
    <th>Orientations</th>
  </tr>
  
  {{foreach from=$medecins item=_medecin}}
  <tr>
  	<td {{if $_medecin->_has_siblings}}style="background: #eef"{{/if}}>{{$_medecin->nom}}</td>
    <td>{{$_medecin->prenom}}</td>
    <td>{{$_medecin->jeunefille}}</td>
  	<td>{{$_medecin->adresse|nl2br}}</td>
  	<td>{{$_medecin->ville}}</td>
  	<td>{{$_medecin->cp}}</td>
  	<td>{{$_medecin->tel}}</td>
  	<td>{{$_medecin->fax}}</td>
  	<td>{{$_medecin->email}}</td>
    <td>{{$_medecin->disciplines|nl2br}}</td>
    <td>{{$_medecin->complementaires|nl2br}}</td>
    <td>{{$_medecin->orientations|nl2br}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}