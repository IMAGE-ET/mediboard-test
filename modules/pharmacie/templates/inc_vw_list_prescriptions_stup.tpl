<table class="tbl">
	{{if $print}}
	<tr>
	  <th class="category" colspan="3">Prescriptions de stupéfiant du {{$date_min|date_format:$dPconfig.date}} au {{$date_max|date_format:$dPconfig.date}}</th>
	</tr>
	{{/if}}
  {{foreach from=$lines item=_line}}
	<tr>
		<td class="text">
			<strong>
			  <a href="#" onclick="Prescription.viewProduit('{{$_line->code_cip}}');">{{$_line->_view}}</a>
			</strong>
		</td>
		<td class="text">
			{{if $_line instanceof CPrescriptionLineMedicament}}
				{{foreach from=$_line->_ref_prises item=_prise name="prises"}}
			    {{$_prise->_view}}{{if !$smarty.foreach.prises.last}},{{/if}}
			  {{/foreach}}
			{{else}}
			  {{$_line->_ref_prescription_line_mix->_view}}
			{{/if}}
		</td>
		<td class="text">
			{{if $_line instanceof CPrescriptionLineMedicament}}
			  {{assign var=prescription value=$_line->_ref_prescription}}
				{{assign var=line_debut value=$_line->debut}}
			{{else}}
			  {{assign var=prescription value=$_line->_ref_prescription_line_mix->_ref_prescription}}
				{{assign var=line_debut value=$_line->_ref_prescription_line_mix->date_debut}}
			{{/if}}
			<a href="#" onclick="viewDossierSoin('{{$prescription->_ref_object->_id}}','{{$line_debut}}');">
			  {{$prescription->_ref_object->_view}}	
			</a>
		</td>
	</tr>
	{{foreachelse}}
	<tr>
		<td colspan="3">
			Aucune prescription de stupéfiant pour les dates sélectionnées
		</td>
	</tr>
	{{/foreach}}
</table>