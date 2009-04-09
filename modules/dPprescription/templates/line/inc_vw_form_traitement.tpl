{{assign var=prescription_id value=$prescription->_id}}
{{if $prescription->type == "sejour"}}
	{{assign var=line_id value=$line->_id}}
	<form id="editLineTraitement-{{$line_id}}" action="?" method="post" name="editLineTraitement-{{$line_id}}">
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	  <input type="hidden" name="prescription_line_medicament_id" value="{{$line_id}}"/>
	  <input type="hidden" name="del" value="0" />
	  {{mb_field object=$line field=traitement_personnel typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg', { 
	    onComplete: function(){ Prescription.reload($prescription_id,'','medicament'); }
	  } );"}}
	  {{mb_label object=$line field=traitement_personnel typeEnum="checkbox"}}
	</form>
{{/if}}