{{* $Id$ *}}

<script type="text/javascript">
  // Explicit form preparation for Ajax loading
  prepareForm(document.editPrescriptionItem);
  regFieldCalendar('editPrescriptionItem', 'date');
</script>

<form name="editPrescriptionItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPlabo" />
<input type="hidden" name="dosql" value="do_prescription_examen_aed" />
<input type="hidden" name="prescription_labo_examen_id" value="{{$prescriptionItem->_id}}" />
<input type="hidden" name="del" value="0" />

{{if !$prescriptionItem->_id}}
<table class="form">
    <th class="title" colspan="2">
      Veuillez sélectioner un examen
    </th>
  </tr>
</table>
{{else}}
{{assign var="examen" value=$prescriptionItem->_ref_examen_labo}}
{{assign var="patient" value=$prescriptionItem->_ref_prescription_labo->_ref_patient}}
<table class="form">
  <tr>
    <th class="title modify" colspan="2">
      Saisie du résultat
    </th>
  </tr>
  <tr>
    <th>{{tr}}CPatient{{/tr}}</th>
    <td>{{mb_value object=$patient field="_view"}}</td>
  </tr>

  <tr>
    <th>{{tr}}CExamenLabo{{/tr}}</th>
    <td>{{mb_value object=$examen field="_view"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$examen field="type"}}</th>
    <td>
      {{mb_value object=$examen field="type"}}
      {{if $examen->_reference_values}} ({{$examen->_reference_values}}) {{/if}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$prescriptionItem field="date"}}</th>
    <td class="date">{{mb_field object=$prescriptionItem field="date" form="editPrescriptionItem"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$prescriptionItem field="resultat"}}</th>
    <td>{{mb_field object=$prescriptionItem field="resultat" prop=$prescriptionItem->_ref_examen_labo->type}}</td>
  </tr>
  <tr>
    <th>
      {{mb_label object=$prescriptionItem field="commentaire"}}
      <br />
      <select name="_helpers_commentaire" size="1" onchange="pasteHelperContent(this)">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$prescriptionItem->_aides.commentaire.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CPrescriptionLaboExamen', this.form.commentaire)" />            
    </th>
    <td>{{mb_field object=$prescriptionItem field="commentaire"}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.Examen.edit() } });">
        Valider
      </button>
    </td>
  </tr>
</table>

</form>
{{/if}}
