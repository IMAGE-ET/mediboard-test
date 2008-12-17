<script type="text/javascript">
Main.add( function(){
	var oFormAddLineCont = document.forms['addLineCont-{{$line->_id}}'];
	var oFormStopLine = document.forms['form-stop-{{$line->_class_name}}-{{$line->_id}}'];
	
	{{if $line->signee}}
	  if(oFormAddLineCont) oFormAddLineCont.show();
	  if(oFormStopLine)    oFormStopLine.show();
	{{else}}
	  if(oFormAddLineCont) oFormAddLineCont.hide();
	  if(oFormStopLine)    oFormStopLine.hide();
	{{/if}}
} );
</script>

{{if $line->_class_name == "CPrescriptionLineElement" || $line->_class_name == "CPrescriptionLineComment"}}
  <!-- Signature d'un element -->
  <form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
    <input type="hidden" name="dosql" value="{{$dosql}}" />
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
    {{if $line->signee}}
      <input type="hidden" name="signee" value="0" />
      <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription_reelle->_id}}','','{{$div_refresh}}') } }  )">Annuler la signature</button>
    {{else}}
      <input type="hidden" name="signee" value="1" />
      <button type="button" class="tick" id="signature_{{$line->_id}}" onclick="submitFormAjax(this.form,'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription_reelle->_id}}','','{{$div_refresh}}') } }  )">Signer</button>  
    {{/if}}
  </form>
{{else}}
  <!-- Signature d'un medicament, dupliquer le medicament s'il deborde sur le sejour -->
  {{if $line->signee}}
    <form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
      <input type="hidden" name="dosql" value="{{$dosql}}" />
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
      <input type="hidden" name="signee" value="0" />
      <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription_reelle->_id}}','','{{$div_refresh}}') } }  )">Annuler la signature</button>
    </form>
  {{else}}
    <form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
      <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="prescription_line_id" value="{{$line->_id}}" />
      <input type="hidden" name="prescription_reelle_id" value="{{$prescription_reelle->_id}}" />
      <input type="hidden" name="mode_pharma" value="0" />
      <button type="button" class="tick" id="signature_{{$line->_id}}" onclick="submitFormAjax(this.form,'systemMsg');">Signer</button>
    </form>
  {{/if}}
{{/if}}
