{{mb_default var=_type value=""}}
{{mb_default var=_host_field value=""}}

{{if $_field == "_view" || $_field == "_shortview"}}
  <span class="draggable hostfield value" 
        data-field="{{$_field}}" 
        data-type="value" 
        data-ex_group_id="{{$ex_group_id}}" 
        data-ex_group_id="{{$ex_group_id}}" 
        data-host_class="{{$host_object->_class}}" 
        {{if $_host_field}}
          data-field_id="{{$_host_field->_id}}"
        {{/if}}>
    <span class="field-name" style="display: none;">{{tr}}{{$host_object->_class}}{{/tr}} - </span>
    {{if $_field == "_view"}} Vue {{else}} Vue courte {{/if}}
  </span>
{{else}}
  {{if !$_type || $_type == "label"}}
    <span class="draggable hostfield label" 
          data-field="{{$_field}}" 
          data-type="label" 
          data-ex_group_id="{{$ex_group_id}}" 
          data-host_class="{{$host_object->_class}}" 
          {{if $_host_field}}
            data-field_id="{{$_host_field->_id}}"
          {{/if}}>
      <span class="field-name" style="display: none;">{{tr}}{{$host_object->_class}}-{{$_field}}{{/tr}}</span>
      [libellé]
    </span>
  {{/if}}
  
  {{if !$_type || $_type == "value"}}
    <span class="draggable hostfield value" 
          data-field="{{$_field}}" 
          data-type="value" 
          data-ex_group_id="{{$ex_group_id}}" 
          data-host_class="{{$host_object->_class}}" 
          {{if $_host_field}}
            data-field_id="{{$_host_field->_id}}"
          {{/if}}>
      <span class="field-name" style="display: none;">{{tr}}{{$host_object->_class}}-{{$_field}}{{/tr}}</span>
      [valeur]
    </span>
  {{/if}}
  
  {{if !$_type}}
    <span class="field-name">{{tr}}{{$host_object->_class}}-{{$_field}}{{/tr}}</span>
  {{/if}}
{{/if}}