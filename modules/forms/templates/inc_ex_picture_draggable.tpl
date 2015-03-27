{{if !$_picture->disabled && $_picture->_ref_file && $_picture->_ref_file->_id}}
  <div class="resizable form-picture" tabIndex="0" data-picture_id="{{$_picture->_id}}"
       style="left:{{$_picture->coord_left}}px; top:{{$_picture->coord_top}}px; width:{{$_picture->coord_width}}px; height:{{$_picture->coord_height}}px; text-align: center;">
    {{mb_include module=forms template=inc_resizable_handles}}
    <div class="overlayed"
         data-picture_id="{{$_picture->_id}}"
         ondblclick="ExPicture.edit({{$_picture->_id}}); Event.stop(event);"
         onclick="ExClass.focusResizable(event, this)"
         unselectable="on"
         onselectstart="return false;"
      >
      <div style="position: relative; width: 100%; height: 100%;">
        <img src="lib/phpThumb/phpThumb.php?src={{$_picture->_ref_file->_file_path}}" style="width: 100%; height: 100%;" />
        {{if $_picture->show_label}}
          {{$_picture->name}}
        {{/if}}
        <div class="overlay"></div>
      </div>
    </div>
  </div>
{{/if}}


