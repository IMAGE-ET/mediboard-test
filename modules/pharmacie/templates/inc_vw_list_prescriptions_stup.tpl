<table class="tbl" id="list-prescriptions-stup">
	<tr>
	  <th class="title" colspan="5">Prescriptions de stupéfiant du {{$date_min|date_format:$conf.date}} au {{$date_max|date_format:$conf.date}}</th>
	</tr>
	<tr>
		<th>{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function="updateListPrescriptions"}}</th>
    <th>{{mb_colonne class="CPrescriptionLineMedicament" field="_ucd_view" order_col=$order_col order_way=$order_way function="updateListPrescriptions"}}</th>
		<th>Posologie</th>
		<th>Début</th>
    <th>Fin</th>
	</tr>
  {{foreach from=$lines item=_line}}
		{{if $_line instanceof CPrescriptionLineMedicament}}
	    {{assign var=prescription value=$_line->_ref_prescription}}
	    {{assign var=line_debut value=$_line->_debut_reel}}
			{{assign var=line_fin value=$_line->_fin_reelle}}
	  {{else}}
	    {{assign var=prescription value=$_line->_ref_prescription_line_mix->_ref_prescription}}
	    {{assign var=line_debut value=$_line->_ref_prescription_line_mix->_debut}}
			{{assign var=line_fin value=$_line->_ref_prescription_line_mix->_fin}}
	  {{/if}}
		<tr>
			<td class="text">
				<a href="#1" onclick="viewDossierSoin('{{$prescription->_ref_object->_id}}','{{$line_debut}}');">
					<span onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_ref_object->_guid}}')">
            {{$prescription->_ref_object->_ref_patient->_view}}
					</span>
				</a>
      </td>
			<td class="text">
				<a href="#1" onclick="Prescription.viewProduit('{{$_line->code_cip}}');">
          <strong onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">
				  	{{$_line->_ucd_view}}
          </strong>
			  </a>
			</td>
			<td class="text">
				{{if $_line instanceof CPrescriptionLineMedicament}}
					{{foreach from=$_line->_ref_prises item=_prise name="prises"}}
				    {{$_prise->_view}}{{if !$smarty.foreach.prises.last}},{{/if}}
				  {{/foreach}}
				{{else}}
				  {{$_line->_posologie}}
				{{/if}}
			</td>
			<td>
				{{if $_line instanceof CPrescriptionLineMedicament}}
          {{mb_value object=$_line field=_debut_reel}}
        {{else}}
          {{mb_value object=$_line->_ref_prescription_line_mix field=_debut}}
        {{/if}}
			</td>
			<td>
        {{if $_line instanceof CPrescriptionLineMedicament}}
          {{mb_value object=$_line field=_fin_reelle}}
        {{else}}
          {{mb_value object=$_line->_ref_prescription_line_mix field=_fin}}
        {{/if}}
      </td>
		</tr>
	{{foreachelse}}
		<tr>
			<td colspan="5" class="empty">
				Aucune prescription de stupéfiant pour les dates sélectionnées
			</td>
		</tr>
	{{/foreach}}
</table>