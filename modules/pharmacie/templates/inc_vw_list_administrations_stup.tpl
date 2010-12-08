<table class="tbl">
	{{if $print}}
  <tr>
    <th class="category" colspan="4">Administrations de stupéfiant du {{$date_min|date_format:$conf.date}} au {{$date_max|date_format:$conf.date}}</th>
  </tr>
  {{/if}}
  {{foreach from=$administrations item=_administration}}
	  {{if $_administration->_ref_object instanceof CPrescriptionLineMedicament}}
		  {{assign var=prescription value=$_administration->_ref_object->_ref_prescription}}
			{{assign var=line_debut value=$_administration->_ref_object->debut}}
		{{else}}
		  {{assign var=prescription value=$_administration->_ref_object->_ref_prescription_line_mix->_ref_prescription}}
			{{assign var=line_debut value=$_administration->_ref_object->_ref_prescription_line_mix->date_debut}}
		{{/if}}
	<tr>
		<td class="text">
			<strong>
				<a href="#" onclick="Prescription.viewProduit('{{$_administration->_ref_object->code_cip}}');">{{$_administration->_ref_object->_view}}</a>
			</strong>
		</td>
		<td class="text">{{$_administration->quantite}} {{$_administration->_unite_prise}}</td>
		<td class="text">
			<a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_ref_object->_guid}}')" onclick="viewDossierSoin('{{$prescription->_ref_object->_id}}','{{$line_debut}}');">
				{{$prescription->_ref_object->_ref_patient->_view}}
			</a>
		</td>
		<td class="text">{{mb_value object=$_administration field="dateTime"}}</td>
	</tr>
	{{foreachelse}}
  <tr>
    <td colspan="4">
      Aucune administration de stupéfiant pour les dates sélectionnées
    </td>
  </tr>
  {{/foreach}}
</table>