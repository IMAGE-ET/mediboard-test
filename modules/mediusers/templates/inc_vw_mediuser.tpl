{{mb_default var=initials}}
{{if $initials == "block"}}
  <span style="background-color: #{{$mediuser->_ref_function->color}};"
    onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser->_shortview}}
  </span>
{{elseif $initials == "border"}}
  <span class="mediuser" style="border-left-color: #{{$mediuser->_ref_function->color}};"
    onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser->_shortview}}
  </span>
{{else}}
  <span class="mediuser" style="border-left-color: #{{$mediuser->_ref_function->color}};"
    onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser}}
  </span>
{{/if}}