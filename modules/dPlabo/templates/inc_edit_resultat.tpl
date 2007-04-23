{{* $Id: $ *}}

{{assign var="examen" value=$prescriptionItem->_ref_examen_labo}}

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
<table class="form">
  <tr>
    <th class="title modify" colspan="2">
      Saisie du résultat
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$examen field="_view"}}</th>
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
    <th>{{mb_label object=$prescriptionItem field="commentaire"}}</th>
    <td>{{mb_field object=$prescriptionItem field="commentaire"}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.Examen.edit() } });">Valider</button></td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th colspan="3" class="title">Autres résultats</th>
  </tr>
  <tr>
    <th>Valeur</th>
    <th>Résultat au</th>
    <th>Prescrit le</th>
  </tr>

  {{foreach from=$siblingItems item="_item"}}
  <tbody class="hoverable">
    <tr {{if $_item->_id == $prescriptionItem->_id}}class="selected"{{/if}}>
      {{if $_item->date}}
      <td {{if $_item->commentaire}}rowspan="2"{{/if}}>
        {{assign var=msgClass value=""}}
        {{if $examen->type == "num"}}
          {{if $_item->_hors_limite}}
          {{assign var=msgClass value="warning"}}
          {{else}}
          {{assign var=msgClass value="message"}}
          {{/if}}
        {{/if}}
        
        <div class="{{$msgClass}}">
        {{mb_value object=$_item field=resultat}} 
        {{mb_value object=$examen field=unite}}
        </div>
      </td>
      <td>{{mb_value object=$_item field=date}}</td>
      {{else}}
      <td colspan="2" style="text-align: center">
        <em>Aucun résultat</em>
      </td>
      {{/if}}
      <td>{{mb_value object=$_item->_ref_prescription_labo field=date format="%d/%m/%Y"}}</td>
    </tr>
    {{if $_item->commentaire}}
    <tr {{if $_item->_id == $prescriptionItem->_id}}class="selected"{{/if}}>
      <td class="text" colspan="2">{{mb_value object=$_item field=commentaire}}</td>
    </tr>
    {{/if}}
  </tbody>
  {{/foreach}}
</table>
{{/if}}
</form>