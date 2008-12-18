{{assign var=voies value="CPrescriptionLineMedicament"|static:"voies"}}
<table class="tbl">
	<tr>
	  <th colspan="3">
	  Voies
	  </th>
	</tr>
	<tr>
	  <th>Libellé</th>
	  <th>Injectable</th>
	  <th>Perfusable</th>
	</tr>
	{{foreach from=$voies key=_voie item=voie}}
	<tr>
	  <td>{{$_voie}}</td>
	  <td style="text-align: center;">{{if $voie.injectable}}X{{else}}-{{/if}}</td>
	  <td style="text-align: center;">{{if $voie.perfusable}}X{{else}}-{{/if}}</td>
	</tr>
	{{/foreach}}
</table>