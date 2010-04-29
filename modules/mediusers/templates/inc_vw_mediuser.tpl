{{if @$initials}}
  <span style="background-color: #{{$mediuser->_ref_function->color}};"
        onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
  	{{$mediuser->_shortview}}
  </span>
{{else}}
  <span class="mediuser" style="border-left-color: #{{$mediuser->_ref_function->color}};"
        onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser}}
  </span>
{{/if}}