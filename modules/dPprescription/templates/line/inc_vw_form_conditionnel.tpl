{{if $prescription->type != "externe"}}
	{{if $line->_can_view_form_conditionnel}}
	  <input name="conditionnel" type="checkbox" {{if $line->conditionnel}}checked="checked"{{/if}} onchange="submitConditionnel('{{$line->_class_name}}','{{$line->_id}}',this.checked)"  />
	  {{mb_label object=$line field="conditionnel"}}
	{{elseif !$line->_protocole}}
	  {{mb_label object=$line field="conditionnel"}}:
	  {{if $line->conditionnel}}Oui{{else}}Non{{/if}} 
	{{/if}}
{{/if}}