<!-- $Id: inc_infos_hospitalisation.tpl 6136 2009-04-21 12:31:36Z phenxdesign $ -->

<div onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
	
{{if $_operation->libelle}}
  <strong>[{{$_operation->libelle}}]<br /></strong>
{{/if}}

{{if $app->user_prefs.dPplanningOp_listeCompacte}}
  {{foreach from=$_operation->_ext_codes_ccam item=_code name=codes}}
  {{$_code->code}}
  {{if !$smarty.foreach.codes.last}}&mdash;{{/if}}
  {{/foreach}}
{{else}}
  {{foreach from=$_operation->_ext_codes_ccam item=_code}}
  {{$_code->code}}
	<em>
  {{if !@$board}}
    : {{$_code->libelleLong}}
  {{/if}}
  {{if @$boardItem}}
    : {{$_code->libelleLong|truncate:50:"...":false}}
  {{/if}}
	</em>
 <br />
  {{/foreach}}
{{/if}}

</div>
