{{if $line->_ref_praticien->_id == $app->user_id}}
  <form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
    <input type="hidden" name="dosql" value="{{$dosql}}" />
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="{{$line->_tbl_key}}" value="{{$line->_id}}" />
    {{if $line->signee}}
      <input type="hidden" name="signee" value="0" />
      <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','{{$div_refresh}}') } }  )">Annuler la signature</button>
    {{else}}
      <input type="hidden" name="signee" value="1" />
      <button type="button" class="tick" onclick="submitFormAjax(this.form,'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','{{$div_refresh}}') } }  )">Signer</button>  
    {{/if}}
  </form>
{{/if}}