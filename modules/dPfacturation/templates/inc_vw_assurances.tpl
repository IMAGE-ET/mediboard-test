{{assign var="patient" value=$facture->_ref_patient}}
<form name="assurances-patient" method="post" action="" > 
  {{mb_class object=$facture}}
  {{mb_key   object=$facture}}
  <table class="main tbl">
    <tr>
      <td style="text-align:right;">
      <button style="" type="button" class="add notext" onclick="Correspondant.edit(0, '{{$facture->_ref_patient->_id}}', refreshAssurance);"></button>
      {{if $facture->_class == "CFactureCabinet" || !$facture->dialyse}}
        {{assign var="type_assur" value=assurance_maladie}}
        {{if $facture->type_facture == "accident"}}
          {{assign var="type_assur" value=assurance_accident}}
        {{/if}}
        {{mb_label object=$facture field=$type_assur}}</td>
        {{mb_include module=facturation template="inc_vw_assurances_patient" object=$facture name=$type_assur}}
      {{else}}
        {{assign var="first_assur"   value=assurance_maladie}}
        {{assign var="seconde_assur" value=assurance_accident}}
        {{if $facture->type_facture == "accident"}}
          {{assign var="first_assur"    value=assurance_accident}}
          {{assign var="seconde_assur"  value=assurance_maladie}}
        {{/if}}
        Assurance de base</td>
        {{mb_include module=facturation template="inc_vw_assurances_patient" object=$facture name=$first_assur}}
        <td style="text-align:right;">Assurance compl.</td>
        {{mb_include module=facturation template="inc_vw_assurances_patient" object=$facture name=$seconde_assur}}
      {{/if}}
    </tr>
    <tr>
      {{if $facture->_class == "CFactureCabinet" || !$facture->dialyse}}
        {{assign var="type_assur" value=send_assur_base}}
        {{if $facture->type_facture == "accident"}}
          {{assign var="type_assur" value=send_assur_compl}}
        {{/if}}
        <td style="text-align:right;">{{mb_label object=$facture field=$type_assur}}</td>
        <td>{{mb_field object=$facture field=$type_assur onchange="return onSubmitFormAjax(this.form);" readonly=$facture->cloture}}</td>
      {{else}}
        {{assign var="first_assur"   value=send_assur_base}}
        {{assign var="seconde_assur" value=send_assur_compl}}
      
        {{if $facture->type_facture == "accident"}}
          {{assign var="first_assur"    value=send_assur_compl}}
          {{assign var="seconde_assur"  value=send_assur_base}}
        {{/if}}
          <td style="text-align:right;">{{mb_label object=$facture field=$first_assur}}</td>
          <td>{{mb_field object=$facture field=$first_assur onchange="return onSubmitFormAjax(this.form);" readonly=$facture->cloture}}</td>
          <td style="text-align:right;">{{mb_label object=$facture field=$seconde_assur}}</td>
          <td>{{mb_field object=$facture field=$seconde_assur onchange="return onSubmitFormAjax(this.form);" readonly=$facture->cloture}}</td>
      {{/if}}
    </tr>
  </table>
</form>