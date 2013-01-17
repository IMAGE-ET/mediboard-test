<tr>
  <th colspan="4" class="category">
    {{if $sejour->patient_id}}
      <button style="float:right;" type="button" class="add notext" onclick="Correspondant.edit(0, '{{$patient->_id}}', reloadAssurance);"></button>
    {{/if}}
     Assurance
   </th>
</tr>
  <tr>
    <th>{{mb_label object=$sejour field=assurance_maladie}}</th>
    {{mb_include module=cabinet template="inc_vw_assurances_patient" object=$sejour name="assurance_maladie" colspan="3"}}
  </tr>
<tr>
  <th>{{mb_label object=$sejour field="rques_assurance_maladie"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="rques_assurance_maladie" onchange="checkAssurances();" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}</td>
</tr>
  <tr>
    <th>{{mb_label object=$sejour field=assurance_accident}}</th>
      {{mb_include module=cabinet template="inc_vw_assurances_patient" object=$sejour name="assurance_accident" colspan="3"}}
  </tr>
<tr>
  <th>{{mb_label object=$sejour field="rques_assurance_accident"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="rques_assurance_accident" onchange="checkAssurances();" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}</td>
</tr>