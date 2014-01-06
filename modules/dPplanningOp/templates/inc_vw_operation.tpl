<!-- $Id: inc_infos_hospitalisation.tpl 6136 2009-04-21 12:31:36Z phenxdesign $ -->

<span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
{{if $_operation->libelle}}
  <strong>{{$_operation->libelle}}<br /></strong>
{{/if}}

{{foreach from=$_operation->_ext_codes_ccam_princ item=_code name=codes}}
  {{if $app->user_prefs.dPplanningOp_listeCompacte}}
    {{$_code->code}}
    {{if !$smarty.foreach.codes.last}}&mdash;{{/if}}
  {{else}}
    {{$_code->code}}
    {{if !@$board}}
      :<em> {{$_code->libelleLong}}</em>
    {{/if}}
    {{if @$boardItem}}
      :<em> {{$_code->libelleLong|truncate:50:"...":false}}</em>
    {{/if}}
    <br />
  {{/if}}
{{/foreach}}

</span>