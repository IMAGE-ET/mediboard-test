{{if $line->_can_view_form_ald}}
  <form action="?" method="post" name="editLineALD-{{$line->_class_name}}-{{$line->_id}}">
     <input type="hidden" name="m" value="dPprescription" />
     <input type="hidden" name="dosql" value="{{$dosql}}" />
     <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
     <input type="hidden" name="del" value="0" />
     {{mb_field object=$line field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
     {{mb_label object=$line field="ald" typeEnum="checkbox"}}
  </form>
{{elseif !$line->_protocole}}
  {{mb_label object=$line field="ald" typeEnum="checkbox"}}:
  {{if $line->ald}}
    Oui
  {{else}}
    Non
  {{/if}} 
{{/if}}	        