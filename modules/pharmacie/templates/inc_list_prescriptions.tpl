<script type="text/javascript">
	Main.add(function(){
	  Control.Tabs.create("tab-prescriptions-pharma", true);
	});
	
 list_prescriptions = {{$list_prescriptions|@json}};
</script>

<tr>
  <th class="title">Prescriptions <small>({{$count_prescriptions}})</small></th>
</tr>
<tr>
	<td>
		<ul class="control_tabs small" id="tab-prescriptions-pharma">
		  {{foreach from=$prescriptions key=type_score item=_prescriptions_by_score}}
			  {{if $_prescriptions_by_score|@count}}
		    <li><a href="#niveau-{{$type_score}}">Score {{tr}}CPrescription.score.{{$type_score}}{{/tr}} - <small>({{$_prescriptions_by_score|@count}})</small></a></li>
				{{/if}}
		  {{/foreach}}
		</ul>
	</td>
</tr>
{{foreach from=$prescriptions key=type_score item=_prescriptions_by_score}}
 {{if $_prescriptions_by_score|@count}}
  <tbody id="niveau-{{$type_score}}">
  <tr>
  {{if $type_score == "0" || $type_score == "1"}}
    <th><button type="button" class="tick" onclick="valideAllPrescriptions(list_prescriptions[{{$type_score}}]);">Tout valider</button></th>
  {{else}}
	  <th><div class="warning">Validation globale impossible</div></th>
	{{/if}}
	</tr> 
  
	{{foreach from=$_prescriptions_by_score item=_prescription}}
	<tr id="prescription_pharma_{{$_prescription->_id}}">
	  <td class="mediuser">
	    <a class="mediuser" style="border-left-color: #{{$_prescription->_ref_object->_ref_praticien->_ref_function->color}};" href="#{{$_prescription->_id}}" onclick="Prescription.reloadPrescPharma('{{$_prescription->_id}}'); markAsSelected(this);">
	      {{assign var=sejour value=$_prescription->_ref_object}}
	      <strong>{{$_prescription->_ref_patient->_view}}</strong>
	      <br />{{$sejour->_shortview}}
	    </a>
	  </td>
	</tr>
	{{foreachelse}}
	<tr>
		<td>
			{{tr}}CPrescription.none{{/tr}}
		</td>
	</tr>
	</tbody>
	{{/foreach}}
	{{/if}}
{{/foreach}}