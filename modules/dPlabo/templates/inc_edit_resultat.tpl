{{* $Id: $ *}}

<form name="editPrescriptionItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPlabo" />
<input type="hidden" name="dosql" value="do_prescription_examen_aed" />
<input type="hidden" name="prescription_labo_examen_id" value="{{$prescriptionItem->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $prescriptionItem->_id}}
    <th class="title modify" colspan="2">
      Saisie du résultat
    </th>
    {{else}}
    <th colspan="2">
      Veuillez sélectioner un examen
    </th>
    {{/if}}
  </tr>
  <tr>
    <th>{{mb_label object=$prescriptionItem field="date"}}</th>
    <td>{{mb_field object=$prescriptionItem field="date"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$prescriptionItem field="resultat"}}</th>
    <td>{{mb_field object=$prescriptionItem field="resultat"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$prescriptionItem field="commentaire"}}</th>
    <td>{{mb_field object=$prescriptionItem field="commentaire"}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg');">Valider</button></td>
  </tr>
</table>

</form>