<div class="draggable {{$_type}}" data-field_id="{{$_field->_id}}">
  <div style="position: relative;">
    {{if $_type == "field"}}
      <div class="field-info" style="display: none;">{{tr}}{{$_field->_locale}}{{/tr}}</div>
			{{mb_field object=$ex_object field=$_field->name register=true increment=true form="form-grid-layout"}}
    {{else}}
      {{mb_label object=$ex_object field=$_field->name}}
    {{/if}}
    <div style="position:absolute;top:0;left:0;bottom:0;right:0;"></div>
  </div>
</div>