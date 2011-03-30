<div class="draggable {{$_type}}" data-type="{{$_type}}" data-field_id="{{$_field->_id}}" ondblclick="ExField.edit({{$_field->_id}})">
  <div style="position: relative;">
    {{if $_type == "field"}}
      <div class="field-info" style="display: none;">{{if $_field->_locale}}{{tr}}{{$_field->_locale}}{{/tr}}{{else}}{{$_field->name}}{{/if}}</div>
			<div class="field-content">
			  {{mb_field object=$ex_object field=$_field->name register=true increment=true form="form-grid-layout" rows=1}}
			</div>
    {{else}}
      {{mb_label object=$ex_object field=$_field->name}}
    {{/if}}
    <div class="overlay"></div>
  </div>
</div>