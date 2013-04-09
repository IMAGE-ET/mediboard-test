<tr>
  <th colspan="4" class="category">
    {{if $sejour->patient_id}}
      <button style="float:right;" type="button" class="add notext" onclick="Correspondant.edit(0, '{{$patient->_id}}', reloadAssurance);"></button>
    {{/if}}
     Assurance
   </th>
</tr>
<tr>
  {{if $conf.dPplanningOp.CFactureEtablissement.show_type_facture}}
    <th>{{mb_label object=$sejour field=_type_sejour}}</th>
    <td>{{mb_field object=$sejour field=_type_sejour onchange="Value.synchronize(this, 'editSejour');"}}</td>
  {{/if}}
  {{if $conf.dPplanningOp.CFactureEtablissement.show_dialyse}}
    <th>{{mb_label object=$sejour field=_dialyse}}</th>
    <td>{{mb_field object=$sejour field=_dialyse onchange="Value.synchronize(this, 'editSejour');"}}</td>
  {{else}}
    <td colspan="2"></td>
  {{/if}}
</tr>
<tr>
  {{if $conf.dPplanningOp.CFactureEtablissement.show_statut_pro}}
    <th>{{mb_label object=$sejour field=_statut_pro}}</th>
    <td>{{mb_field object=$sejour field=_statut_pro emptyLabel="Choisir un status" onchange="Value.synchronize(this, 'editSejour');"}}</td>
  {{/if}}
  {{if $conf.dPplanningOp.CFactureEtablissement.show_cession}}
    <th>{{mb_label object=$sejour field=_cession_creance}}</th>
    <td>{{mb_field object=$sejour field=_cession_creance onchange="Value.synchronize(this, 'editSejour');"}}</td>
  {{/if}}
</tr>

<tr>
  <th>{{mb_label object=$sejour field=_assurance_maladie}}</th>
  {{mb_include module=facturation template="inc_vw_assurances_patient" object=$sejour name="_assurance_maladie" colspan="3"}}
</tr>
<tr>
  <th>{{mb_label object=$sejour field="_rques_assurance_maladie"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="_rques_assurance_maladie" onchange="Value.synchronize(this, 'editSejour');checkAssurances();" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}</td>
</tr>

{{if $conf.dPplanningOp.CFactureEtablissement.show_assur_accident}}
  <tr>
    <th>{{mb_label object=$sejour field=_assurance_accident}}</th>
      {{mb_include module=facturation template="inc_vw_assurances_patient" object=$sejour name="_assurance_accident" colspan="3"}}
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="_rques_assurance_accident"}}</th>
    <td colspan="3">
      {{mb_field object=$sejour field="_rques_assurance_accident" onchange="Value.synchronize(this, 'editSejour');checkAssurances();" form="editSejour"
          aidesaisie="validateOnBlur: 0"}}</td>
  </tr>
{{/if}}