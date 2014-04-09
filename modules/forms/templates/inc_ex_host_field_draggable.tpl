{{mb_default var=_type value=""}}
{{mb_default var=_host_field value=""}}
{{mb_default var=layout_editor value=false}}

{{if $_field == "_view" || $_field == "_shortview"}}
  <span class="{{if !$ex_class->pixel_positionning}} draggable {{/if}} hostfield value overlayed"
        data-field="{{$_field}}" 
        data-type="value" 
        data-ex_group_id="{{$ex_group_id}}" 
        data-ex_class_id="{{$ex_class->_id}}"
        data-host_class="{{$host_object->_class}}"
        {{if $_host_field}}
          data-field_id="{{$_host_field->_id}}"
        {{/if}}
        ondblclick="ExClassHostField.del(this); Event.stop(event);"
        onclick="ExClass.focusResizable(event, this)"
    >
    {{if $layout_editor && $ex_class->pixel_positionning}}
      <button class="add compact" type="button" onclick="ExClassHostField.create(this.up('.hostfield'))">
        {{if $_field == "_view"}}Vue{{else}}Vue courte{{/if}}
      </button>
    {{else}}
      <span class="field-name" style="display: none;">{{tr}}{{$host_object->_class}}{{/tr}} - </span>
      {{if $_field == "_view"}} Vue {{else}} Vue courte {{/if}}
      <div class="overlay"></div>
    {{/if}}
  </span>
{{else}}
  {{if !$_type || $_type == "label"}}
    <span class="{{if !$ex_class->pixel_positionning}} draggable {{/if}} hostfield label overlayed"
          data-field="{{$_field}}" 
          data-type="label" 
          data-ex_group_id="{{$ex_group_id}}"
          data-ex_class_id="{{$ex_class->_id}}"
          data-host_class="{{$host_object->_class}}" 
          {{if $_host_field}}
            data-field_id="{{$_host_field->_id}}"
          {{/if}}
          ondblclick="ExClassHostField.del(this); Event.stop(event);"
          onclick="ExClass.focusResizable(event, this)"
      >
      {{if $layout_editor && $ex_class->pixel_positionning}}
        <button class="add compact" type="button" onclick="ExClassHostField.create(this.up('.hostfield'))">
          libellé
        </button>
      {{else}}
        <span class="field-name" style="display: none;">{{tr}}{{$host_object->_class}}-{{$_field}}{{/tr}}</span>
        [libellé]
        <div class="overlay"></div>
      {{/if}}
    </span>
  {{/if}}
  
  {{if !$_type || $_type == "value"}}
    <span class="{{if !$ex_class->pixel_positionning}} draggable {{/if}} hostfield value overlayed"
          data-field="{{$_field}}" 
          data-type="value" 
          data-ex_group_id="{{$ex_group_id}}"
          data-ex_class_id="{{$ex_class->_id}}"
          data-host_class="{{$host_object->_class}}" 
          {{if $_host_field}}
            data-field_id="{{$_host_field->_id}}"
          {{/if}}
          ondblclick="ExClassHostField.del(this); Event.stop(event);"
          onclick="ExClass.focusResizable(event, this)"
      >
      {{if $layout_editor && $ex_class->pixel_positionning}}
        <button class="add compact" type="button" onclick="ExClassHostField.create(this.up('.hostfield'))">
          valeur
        </button>
      {{else}}
        <span class="field-name" style="display: none;">{{tr}}{{$host_object->_class}}-{{$_field}}{{/tr}}</span>
        [valeur]
        <div class="overlay"></div>
      {{/if}}
    </span>
  {{/if}}
  
  {{if !$_type}}
    <span class="field-name">{{tr}}{{$host_object->_class}}-{{$_field}}{{/tr}}</span>
  {{/if}}
{{/if}}