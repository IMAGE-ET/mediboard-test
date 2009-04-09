{{if $prescription->object_class == "CSejour" && $prescription->type != "sortie"}}
	{{if $line->_perm_edit}}
	  {{assign var=line_class value=$line->_class_name}}
	  {{assign var=line_id value=$line->_id}}
	  {{assign var=line_commentaire value=$line->commentaire}}
	  
	  {{if $line->_class_name == "CPrescriptionLineMedicament" && $line->substitute_for_id && !$line->substitution_active}}
	    {{mb_field object=$line field="emplacement" onchange="submitEditEmplacementSubst('$line_id',this.value);"}}
	  {{else}}
	    {{mb_field object=$line field="emplacement" onchange="submitEmplacement('$line_class','$line_id',this.value);"}}
	  {{/if}}
	{{else}}
	  {{mb_value object=$line field="emplacement"}}
	{{/if}}
{{/if}}


