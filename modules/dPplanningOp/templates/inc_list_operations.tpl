{{*mb_include_script module="dPcompteRendu" script="document"*}}

<input id="currDateJSAccess" name="currDateJSAccess" type="hidden" value="{{$date}}" />
{{if !$board}}
<div style="font-weight:bold; height:20px; text-align:center;">
  {{$date|date_format:$dPconfig.longdate}}
  <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
  <script type="text/javascript">
    Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab=vw_idx_planning&date=");
  </script>
</div>
{{/if}}


<table class="tbl" {{if $boardItem}}style="font-size: 9px;"{{/if}}>
  <tr>
    <th class="title" colspan="5">
       Interventions
    </th>
  </tr>
  
  <tr>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>
      [{{mb_label class=COperation field=libelle}}] 
      {{mb_label class=COperation field=codes_ccam}}
    </th>
    <th>{{mb_title class=COperation field=time_operation}}</th>
    <th>{{mb_title class=COperation field=temp_operation}}</th>
  </tr>
  {{foreach from=$listDay item=curr_plage}}
  <tr>
    <th colspan="6">{{$curr_plage->_ref_salle->nom}} : de {{$curr_plage->debut|date_format:$dPconfig.time}} à {{$curr_plage->fin|date_format:$dPconfig.time}}</th>
  </tr>
  
  {{foreach from=$curr_plage->_ref_operations item=curr_op}}
  <tbody class="hoverable">
  
  <tr>
    {{assign var=patient value=$curr_op->_ref_sejour->_ref_patient}}
    <td class="text" {{if !$board}}rowspan="2"{{/if}}>
    
      <a href="{{$patient->_dossier_cabinet_url}}"
        class="tooltip-trigger"
        onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient->_view}}
      </a>
    </td>
    
    
    <td class="text">
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
        {{if $curr_op->libelle}}
          <em>[{{$curr_op->libelle}}]</em>
          <br />
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <strong>{{$curr_code->code}}</strong>
        {{if !$board}}
        : {{$curr_code->libelleLong}}
        {{/if}}
        {{if $boardItem}}
        : {{$curr_code->libelleLong|truncate:50:"...":false}}
        {{/if}}
        <br />
        {{/foreach}}
      </a>
    </td>

    {{if $curr_op->annulee}}
    <td class="cancelled" colspan="2">
      [ANNULEE]
    </td>

    {{else}}
    <td style="text-align: center;">
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
        {{if $curr_op->time_operation != "00:00:00"}}
          Validé pour 
          <br/ >{{$curr_op->time_operation|date_format:$dPconfig.time}}
        {{else}}
          Non validé
        {{/if}}
        <br />
        {{if $curr_op->horaire_voulu}}
        souhaité pour 
        <br/>{{$curr_op->horaire_voulu|date_format:$dPconfig.time}}
        {{/if}}
      </a>
    </td>
    <td style="text-align: center;">
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
        {{$curr_op->temp_operation|date_format:$dPconfig.time}}
      </a>
    </td>
    {{/if}}
  </tr>
  
  {{if !$board}}
  {{if !$curr_op->annulee}}
	<tr>
    <td colspan="10">
      {{include file=inc_documents_operation.tpl operation=$curr_op}}
    </td>
  </tr>
  {{/if}}
  {{/if}}
  
  </tbody>
  {{/foreach}}
  {{/foreach}}
  
  {{if $listUrgences|@count}}
  <tr>
    <th colspan="6">Hors plage</th>
  </tr>
  {{/if}}
  {{foreach from=$listUrgences item=curr_op}}
  <tbody class="hoverable">

  <tr>
    {{assign var=patient value=$curr_op->_ref_sejour->_ref_patient}}
    <td class="text" {{if !$board}}rowspan="2"{{/if}}>
      <a href="{{$patient->_dossier_cabinet_url}}"
        class="tooltip-trigger"
        onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient->_view}}
      </a>
    </td>
    
    <td class="text">
      <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
        {{if $curr_op->libelle}}
          <em>[{{$curr_op->libelle}}]</em>
          <br />
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <strong>{{$curr_code->code}}</strong>
        {{if !$board}}
        : {{$curr_code->libelleLong}}
        {{/if}}
        <br />
        {{/foreach}}
      </a>
    </td>
    <td style="text-align: center;" {{if $curr_op->annulee}}class="cancelled"{{/if}}>
      {{if $curr_op->annulee}}
        [ANNULEE]
      {{else}}
      <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
        {{$curr_op->_datetime|date_format:"le %d/%m/%Y à %Hh%M"}}
      </a>
      {{/if}}
    </td>
    <td style="text-align: center;">
      <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
        {{$curr_op->temp_operation|date_format:$dPconfig.time}}
      </a>
    </td>
  </tr>

  {{if !$board}}
	<tr>
    <td colspan="10">
      {{include file=inc_documents_operation.tpl operation=$curr_op}}
    </td>
  </tr>
  {{/if}}

  {{/foreach}}
</table>
  