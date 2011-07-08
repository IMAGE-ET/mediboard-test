<table class="tbl" id="list-administrations-stup">
  <tr>
    <th class="title" colspan="6">Administrations de stupéfiant du {{$date_min|date_format:$conf.date}} au {{$date_max|date_format:$conf.date}}</th>
  </tr>
	<tr>
    <th>{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function="updateListAdministrations"}}</th>
    <th>{{mb_colonne class="CPrescriptionLineMedicament" field="_ucd_view" order_col=$order_col order_way=$order_way function="updateListAdministrations"}}</th>
    <th>Administration</th>
    <th>{{mb_colonne class="CAdministration" field="dateTime" order_col=$order_col order_way=$order_way function="updateListAdministrations"}}</th>
		<th>Praticien</th>
		<th>Utilisateur</th>
  </tr>
	
  {{foreach from=$administrations item=_administration}}
	  {{assign var=line value=$_administration->_ref_object}}
	  {{if $line instanceof CPrescriptionLineMedicament}}
		  {{assign var=prescription value=$line->_ref_prescription}}
			{{assign var=line_debut value=$line->debut}}
			{{assign var=praticien value=$line->_ref_praticien}}
		{{else}}
		  {{assign var=prescription value=$line->_ref_prescription_line_mix->_ref_prescription}}
			{{assign var=line_debut value=$line->_ref_prescription_line_mix->date_debut}}
			{{assign var=praticien value=$line->_ref_prescription_line_mix->_ref_praticien}}
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
				<td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
        </td>
			  <td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_administration->_ref_administrateur}}
        </td>
			</tr>
	  {{/if}}
	{{foreachelse}}
  <tr>
    <td colspan="6" class="empty">
      Aucune administration de stupéfiant pour les dates sélectionnées
    </td>
  </tr>
  {{/foreach}}
</table>