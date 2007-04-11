{{* $Id: tree_catalogues.tpl 1792 2007-04-10 16:16:47Z MyttO $
  Parameters
  $_catalogue : catalog to display hierarchically
  $pere_id : selected catalog 
  $catalogue_id to exclude : selected catalog 
*}}

{{if $catalogue_id != $_catalogue->_id}}
<option value="{{$_catalogue->_id}}" 
  {{if $_catalogue->_id == $pere_id}}selected="selected"{{/if}}
  style="padding-left: {{$_catalogue->_level}}em"
>
  {{$_catalogue->_view}} (pere: {{$pere_id}} - self: {{$_catalogue->_id}}
</option>

{{foreach from=$_catalogue->_ref_catalogues_labo item="_catalogue"}}
{{include file="options_catalogues.tpl"}}
{{/foreach}}
{{/if}}
