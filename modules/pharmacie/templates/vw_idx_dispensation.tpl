<table class="tbl">
  <tr>
    <th class="title" colspan="5">Dispensation des médicaments</th>
  </tr>
	<tr>
	  <th>Quantité de la prise</th>
	  <th>Quantité calculée (unité de référence)</th>
	  <th>Nb de boites</th>
	  <th>Dispensation</th>
	</tr>
	{{foreach from=$dispensations key=code_cip item=unites}}
	  {{assign var=medicament value=$medicaments.$code_cip}}
	  <tr>
	    <th colspan="5">{{$medicament->libelle}}</th>
	  </tr>
	  <tbody class="hoverable">
	  {{foreach from=$unites key=unite_prise item=quantite name="dispensation"}}
		  <tr>
		    <td>{{$quantite}} {{$unite_prise}}</td>
		    <td>
		    {{if array_key_exists($code_cip,$quantites_traduites) && array_key_exists($unite_prise, $quantites_traduites.$code_cip)}}
		      {{$quantites_traduites.$code_cip.$unite_prise}} {{$medicament->libelle_unite_presentation}}
		    {{/if}}
		    </td>
	      {{if $smarty.foreach.dispensation.first}}
	      <td rowspan="{{$unites|@count}}" style="text-align: center">{{$quantites.$code_cip}} {{$medicament->libelle_conditionnement}}</td>
		    <td rowspan="{{$unites|@count}}" style="text-align: center"><button type="button" class="submit">Dispenser</button></td>
		    {{/if}}
		  </tr>
	  {{/foreach}}
	  </tbody>
	{{/foreach}}
</table>