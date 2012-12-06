<div class="{{if !$ex_class->pixel_positionning}} draggable {{/if}} {{$_type}} overlayed"
     data-type="{{$_type}}"
     data-message_id="{{$_field->_id}}"
     ondblclick="ExMessage.edit({{$_field->_id}}); Event.stop(event);"
     onclick="this.up('.resizable').focus(); Event.stop(event);"
     unselectable="on"
     onselectstart="return false;"
  >
  <div style="position: relative;">
    {{if $_type == "message_title"}}
      <div class="field-info" style="display: none;">{{$_field->title}}</div>
      <div class="field-content">{{$_field->title}}</div>
    {{else}}
      {{mb_include module=forms template=inc_ex_message _message=$_field}}
    {{/if}}
    <div class="overlay"></div>
  </div>
</div>