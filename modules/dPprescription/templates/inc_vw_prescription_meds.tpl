{{if $prescription->_id}}
	<table class="tbl">
	  <tr>
	    <th>Alerte (puce / hors LT)</th>
	    <th>Médicament</th>
	    <th>Praticien</th>
	    <th>Début</th>
	    <th>Durée</th>
	  </tr>
	  {{foreach from=$prescription->_ref_prescription_lines item=curr_line}}
	  <tr>
	    <td />
	    <td>{{$curr_line->_ucd_view}}</td>
	    <td>Dr {{$curr_line->_ref_praticien->_view}}</td>
	    <td>{{$curr_line->debut|date_format:"%d/%m/%Y"}}</td>
	    <td>{{if $curr_line->duree}}{{$curr_line->duree}}{{else}}0{{/if}} jour(s)</td>
	  </tr>
	  {{/foreach}}
	</table>
{{else}}
  <div class="small-info">
  Aucune prescription de séjour
  </div>
{{/if}}