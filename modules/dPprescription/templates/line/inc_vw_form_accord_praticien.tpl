{{if !$this->valide_pharma}}
	<form action="?" method="post" name="editLineAccordPraticien-{{$line->_id}}">
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="dosql" value="{{$dosql}}" />
	  <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
	  <input type="hidden" name="del" value="0" />
	  {{mb_field object=$line field="accord_praticien" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
	  {{mb_label object=$line field="accord_praticien" typeEnum="checkbox"}}
	</form> 
{{else}}
  {{if $line->accord_praticien}}
    En accord avec le praticien
  {{/if}}
{{/if}}	            