<div class="draggable {{$_type}}" data-type="{{$_type}}" data-message_id="{{$_field->_id}}">
  <div style="position: relative;">
    {{if $_type == "message_title"}}
      <div class="field-info" style="display: none;">{{$_field->title}}</div>
      <div class="field-content">
      	{{$_field->title}}
      </div>
    {{else}}
      <div class="small-{{$_field->type}}">{{mb_value object=$_field field=text}}</div>
    {{/if}}
    <div class="overlay"></div>
  </div>
</div>