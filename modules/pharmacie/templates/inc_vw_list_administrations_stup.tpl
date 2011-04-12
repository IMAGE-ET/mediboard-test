<table class="tbl" id="list-administrations-stup">
  <tr>
    <th class="title" colspan="4">Administrations de stupéfiant du {{$date_min|date_format:$conf.date}} au {{$date_max|date_format:$conf.date}}</th>
  </tr>
	<tr>
    <th>{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function="updateListAdministrations"}}</th>
    <th>{{mb_colonne class="CPrescriptionLineMedicament" field="_ucd_view" order_col=$order_col order_way=$order_way function="updateListAdministrations"}}</th>
    <th>Administration</th>
    <th>{{mb_colonne class="CAdministration" field="dateTime" order_col=$order_col order_way=$order_way function="updateListAdministrations"}}</th>
  </tr>
	
  {{foreach from=$administrations item=_administration}}
	  {{if $_administration->_ref_object instanceof CPrescriptionLineMedicament}}
		  {{assign var=prescription value=$_administration->_ref_object->_ref_prescription}}
			{{assign var=line_debut value=$_administration->_ref_object->debut}}
		{{else}}
		  {{assign var=prescription value=$_administration->_ref_object->_ref_prescription_line_mix->_ref_prescription}}
			{{assign var=line_debut value=$_administration->_ref_object->_ref_prescription_line_mix->date_debut}}
		{{/if}}
		{{if $_administration->quantite}}
			<tr>
		    <td class="text">
		      <a href="#" onclick="viewDossierSoin('{{$prescription->_ref_object->_id}}','{{$line_debut}}');">
		        <span onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_ref_object->_guid}}')">
		        	{{$prescription->_ref_object->_ref_patient->_view}}
		        </span>
		      </a>
		    </td>
				<td class="text">
					<strong>
						<a href="#" onclick="Prescription.viewProduit('{{$_administration->_ref_object->code_cip}}');">
							{{$_administration->_ref_object->_view}}
						</a>
					</strong>
				</td>
				<td class="text">
					{{$_administration->quantite}} {{$_administration->_unite_prise}}
				</td>
				<td class="text">
					{{mb_value object=$_administration field="dateTime"}}
				</td>
			</tr>
	  {{/if}}
	{{foreachelse}}
  <tr>
    <td colspan="4" class="empty">
      Aucune administration de stupéfiant pour les dates sélectionnées
    </td>
  </tr>
  {{/foreach}}
</table>