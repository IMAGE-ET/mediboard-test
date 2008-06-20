<form name="validation_infirmiere-{{$line->_id}}" action="" method="post">
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
  {{if $line->valide_infirmiere}}
    (Validée par l'infirmière)
    <!-- 
    <input type="hidden" name="valide_infirmiere" value="0" />
    <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','{{$div_refresh}}','','{{$mode_pharma}}') } }  )">Annuler la validation infirmière</button>
     -->
  {{else}}
    <input type="hidden" name="valide_infirmiere" value="1" />
    <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','{{$div_refresh}}','','{{$mode_pharma}}') } }  )">Validation infirmière</button>
  {{/if}}
</form>