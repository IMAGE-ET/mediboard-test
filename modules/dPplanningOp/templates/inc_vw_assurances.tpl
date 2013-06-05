<tr>
  <th colspan="4" class="category">
    {{if $sejour->patient_id}}
      <button style="float:right;" type="button" class="add notext" onclick="Correspondant.edit(0, '{{$patient->_id}}', null);"></button>
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
<script>
  Main.add(function(){
    var form = getForm('{{$form}}');
    var urlmaladie = new Url('dPpatients', 'ajax_correspondant_autocomplete');
    urlmaladie.addParam('patient_id', '{{$sejour->patient_id}}');
    urlmaladie.addParam('type', '_assurance_maladie_view');
    urlmaladie.autoComplete(form._assurance_maladie_view, null, {
      minChars: 0,
      dropdown: true,
      select: "newcode",
      updateElement: function(selected) {
        $V(form._assurance_maladie_view, selected.down(".newcode").getText(), false);
        $V(form._assurance_maladie, selected.down(".newcode").get("id"), false);
        {{if $form == "editOpEasy"}}
          var form2 = getForm('editSejour');
          $V(form2._assurance_maladie, selected.down(".newcode").get("id"), false);
        {{/if}}
      }
    });
  });
</script>
<tr>
  <th>{{mb_label object=$sejour field=_assurance_maladie}}</th>
  <td>
    <input type="hidden" name="_assurance_maladie" value="{{$sejour->_assurance_maladie}}"/>
    <input type="text" name="_assurance_maladie_view"
           value="{{if $sejour->_ref_factures|@count}}{{$sejour->_ref_last_facture->_ref_assurance_maladie->nom}}{{/if}}"/>
  </td>
</tr>
<tr>
  <th>{{mb_label object=$sejour field="_rques_assurance_maladie"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="_rques_assurance_maladie" onchange="Value.synchronize(this, 'editSejour');checkAssurances();"
      form="editSejour" aidesaisie="validateOnBlur: 0"}}
  </td>
</tr>

{{if $conf.dPplanningOp.CFactureEtablissement.show_assur_accident}}
  <script>
    Main.add(function(){
      var form = getForm('{{$form}}');
      var urlaccident = new Url('dPpatients', 'ajax_correspondant_autocomplete');
      urlaccident.addParam('patient_id', '{{$sejour->patient_id}}');
      urlaccident.addParam('type', '_assurance_accident_view');
      urlaccident.autoComplete(form._assurance_accident_view, null, {
        minChars: 0,
        dropdown: true,
        select: "newcode",
        updateElement: function(selected) {
          $V(form._assurance_accident_view, selected.down(".newcode").getText(), false);
          $V(form._assurance_accident, selected.down(".newcode").get("id"), false);
        }
      });
    });
  </script>
  <tr>
    <th>{{mb_label object=$sejour field=_assurance_accident}}</th>
    <td>
      <input type="hidden" name="_assurance_accident" value="{{$sejour->_assurance_accident}}"/>
      <input type="text" name="_assurance_accident_view"
             value="{{if $sejour->_ref_factures|@count}}{{$sejour->_ref_last_facture->_ref_assurance_accident->nom}}{{/if}}"/>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="_rques_assurance_accident"}}</th>
    <td colspan="3">
      {{mb_field object=$sejour field="_rques_assurance_accident" onchange="Value.synchronize(this, 'editSejour');checkAssurances();"
        form="editSejour" aidesaisie="validateOnBlur: 0"}}
    </td>
  </tr>
{{/if}}