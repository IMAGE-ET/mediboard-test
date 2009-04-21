{{* $Id$
  -- Parameters
  $_catalogue : catalog to display hierarchically
  $selected_id : selected catalog 
  $exclude_id to exclude : selected catalog 
*}}

{{if $exclude_id != $_catalogue->_id}}
<option value="{{$_catalogue->_id}}" 
  {{if $_catalogue->_id == $selected_id}}selected="selected"{{/if}}
  style="padding-left: {{$_catalogue->_level * 2}}em;"
>
  {{tr}}CExamen-catalogue-{{$_catalogue->_level}}{{/tr}} :
  {{$_catalogue->_view}}
</option>

{{foreach from=$_catalogue->_ref_catalogues_labo item="_catalogue"}}
{{include file="options_catalogues.tpl"}}
{{/foreach}}
{{/if}}
