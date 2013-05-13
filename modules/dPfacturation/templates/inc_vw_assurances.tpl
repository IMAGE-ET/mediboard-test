{{assign var="patient" value=$facture->_ref_patient}}
<form name="assurances-patient" method="post" action="" > 
  {{mb_class object=$facture}}
  {{mb_key   object=$facture}}
  <table class="main tbl">
    <tr>
      <td style="text-align:right;">
      <button style="" type="button" class="add notext" onclick="Correspondant.edit(0, '{{$facture->_ref_patient->_id}}', refreshAssurance);"></button>
      {{mb_label object=$facture field=assurance_maladie}}</td>
      {{mb_include module=facturation template="inc_vw_assurances_patient" object=$facture name="assurance_maladie"}}
      
      <td style="text-align:right;"> {{mb_label object=$facture field=assurance_accident}}</td>
      {{mb_include module=facturation template="inc_vw_assurances_patient" object=$facture name="assurance_accident"}}
    </tr>
    <tr>
      <td style="text-align:right;">{{mb_label object=$facture field=send_assur_base}}</td>
      <td>
        {{mb_field object=$facture field=send_assur_base onchange="return onSubmitFormAjax(this.form);" readonly=$facture->cloture}}
      </td>
      <td style="text-align:right;">{{mb_label object=$facture field=send_assur_compl}}</td>
      <td>
        {{mb_field object=$facture field=send_assur_compl onchange="return onSubmitFormAjax(this.form);" readonly=$facture->cloture}}
    </td>
    </tr>
  </table>
</form>