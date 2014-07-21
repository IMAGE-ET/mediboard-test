{{mb_default var=initials}}
{{if $initials == "block"}}
  <span style="background-color: #{{$mediuser->_color}};"
    onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser->_shortview}}</span> {{* no space before </span> for the DOM to eat the space after the text (for the border-bottom) *}}
{{elseif $initials == "border"}}
  <span class="mediuser" style="border-left-color: #{{$mediuser->_color}};"
    onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser->_shortview}}</span>
{{else}}
  <span class="mediuser" style="border-left-color: #{{$mediuser->_color}};"
    onmouseover="ObjectTooltip.createEx(this, '{{$mediuser->_guid}}')">
    {{$mediuser}}</span>
{{/if}}