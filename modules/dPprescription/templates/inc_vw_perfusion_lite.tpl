<table class="tbl">
{{assign var=perfusion_id value=$_perfusion->_id}}
<tr>
  <td style="width: 10%;" {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}class="arretee"{{/if}}>
			{{if $_perfusion->_ref_parent_line->_id}}
        {{assign var=parent_perf value=$_perfusion->_ref_parent_line}}
        <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
             class="tooltip-trigger" 
             onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$parent_perf->_class_name}}', object_id: '{{$parent_perf->_id}}' } })"/>
      {{/if}}   
    {{mb_value object=$_perfusion field=type}}
  </td>
  <td style="width: 10%;">{{mb_value object=$_perfusion field=vitesse}} ml/h</td>
  <td style="width: 15%;">{{mb_value object=$_perfusion field=voie}}</td>
  <td style="width: 10%;">{{mb_value object=$_perfusion field=date_debut}}</td>
  <td style="width: 10%;">{{mb_value object=$_perfusion field=duree}} heures</td>
  <td style="width: 55%;" class="text">
    <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, true,'{{$_perfusion->_guid}}');"></button>
      
    {{foreach from=$_perfusion->_ref_lines item=_perf_line name=lines}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$_perf_line}}
      {{$_perf_line->_ucd_view}} {{if !$smarty.foreach.lines.last}},{{/if}}
    {{/foreach}}
  </td>    
</tr>
</table>