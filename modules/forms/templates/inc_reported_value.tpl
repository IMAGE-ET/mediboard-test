{{assign var=field_name value=$ex_field->name}}

{{if $ex_field->report_class}}
  {{if $ex_object->_id}}
    <img src="./images/icons/reported.png" title="Valeur reportée ({{tr}}{{$ex_field->report_class}}{{/tr}})" />
  {{else}}
    {{assign var=reported_from value=$ex_object->_reported_fields.$field_name}}
    
    {{if $reported_from}}
      {{if $reported_from instanceof CExObject}}
        <img src="./images/icons/reported.png" style="outline: 0 solid green; background: #7f7;"
             title="Valeur reportée depuis {{$reported_from->_ref_ex_class->name}}&#10;{{mb_value object=$reported_from->_ref_last_log field=date}}&#10;{{$reported_from->_ref_object}}"  />
      {{else}}
        <img src="./images/icons/reported.png" style="outline: 0 solid blue; background: #77f;"
             title="Valeur reportée depuis {{$reported_from->_view}}"  />
      {{/if}}
    {{else}}
      <img src="./images/icons/reported.png" title="Valeur non reportée" class="opacity-50" />
    {{/if}}
  {{/if}}
{{/if}}