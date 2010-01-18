<tr>
  <th>Prescriptions ({{$prescriptions|@count}})</th>
</tr>
{{foreach from=$prescriptions item=_prescription}}
<tr id="prescription_pharma_{{$_prescription->_id}}">
  <td class="mediuser" >
    <a class="mediuser" style="border-left-color: #{{$_prescription->_ref_object->_ref_praticien->_ref_function->color}};" href="#{{$_prescription->_id}}" onclick="Prescription.reloadPrescPharma('{{$_prescription->_id}}',true,{{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}}); markAsSelected(this);">
      {{assign var=sejour value=$_prescription->_ref_object}}
      <strong>{{$_prescription->_ref_patient->_view}}</strong>
      <br />{{$sejour->_shortview}}
    </a>
  </td>
</tr>
{{foreachelse}}
<tr>
  <td colspan="3">{{tr}}CPrescription.none{{/tr}}</td>
</tr>
{{/foreach}}