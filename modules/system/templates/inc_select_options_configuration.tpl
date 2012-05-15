{{foreach from=$items item=_item}}
  <option value="{{$_item.object->_guid}}" {{if $level == 0}}style="font-weight: bold;"{{/if}}>
    {{"&nbsp;&nbsp;&nbsp;"|str_repeat:$level}}{{if $level > 0}}|&ndash;{{/if}}
    {{$_item.object->_view}}
  </option>
  
  {{mb_include module=system template=inc_select_options_configuration items=$_item.children level=$level+1}}
{{/foreach}}
