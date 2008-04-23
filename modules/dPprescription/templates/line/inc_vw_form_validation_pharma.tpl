<form name="validation_pharma-{{$line->_id}}" action="" method="post">
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_medicament_id" value="{{$line->_id}}" />
  {{if $line->valide_pharma}}
    <input type="hidden" name="valide_pharma" value="0" />
    <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','medicament','','{{$mode_pharma}}') } }  )">Annuler la validation pharmacien</button>
  {{else}}
    <input type="hidden" name="valide_pharma" value="1" />
      <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','medicament','','{{$mode_pharma}}') } }  )">Validation pharmacien</button>
  {{/if}}
</form>