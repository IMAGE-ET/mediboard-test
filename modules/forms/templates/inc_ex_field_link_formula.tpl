{{* if a formula that contains more than only a constant *}}
{{if $ex_field->formula && "/[^A-z]/"|preg_match:$ex_field->formula && ($spec instanceof CDateSpec || $spec instanceof CTimeSpec || $spec instanceof CDateTimeSpec)}}
  {{unique_id var=checkbox_formula_uid}}
  {{assign var=field_name value=$ex_field->name}}

  <label title="Lier à la formule '{{$ex_field->_formula|JSAttribute}}'">
    <img src="style/mediboard/images/buttons/formula.png" /><input class="date-toggle-formula"
     type="checkbox"
     data-toggle-formula-for="{{$field_name}}"
     id="cb-{{$checkbox_formula_uid}}"
     {{if !$ex_object->_id || $ex_object->$field_name == ""}} checked {{/if}}
    />
  </label>
{{/if}}
