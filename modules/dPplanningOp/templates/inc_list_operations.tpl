{{if !$board}}
<script type="text/javascript">
  Main.add(function(){
    Calendar.regField(getForm("changeDate").date, null, {noView: true});
  });
</script>

<form action="?" name="changeDate" method="get" style="font-weight:bold; padding: 2px; text-align:center; display: block;">
  {{$date|date_format:$dPconfig.longdate}}
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_idx_planning" />
  <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
</form>

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
    <th colspan="6">
    	{{$curr_plage->_ref_salle->nom}} : 
    	de {{$curr_plage->debut|date_format:$dPconfig.time}} 
    	à {{$curr_plage->fin|date_format:$dPconfig.time}}
    </th>
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
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}" onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
        {{if $curr_op->libelle}}
          <em>[{{$curr_op->libelle}}]</em>
        {{/if}}

	    	{{if $app->user_prefs.dPplanningOp_listeCompacte}}
	        {{foreach from=$curr_op->_ext_codes_ccam item=_code name=codes}}
	        <strong>{{$_code->code}}</strong>
	        {{if !$smarty.foreach.codes.last}}&mdash;{{/if}}
	        {{/foreach}}
	      {{else}}
	        {{foreach from=$curr_op->_ext_codes_ccam item=_code}}
	        <br />
	        <strong>{{$_code->code}}</strong>
	        {{if !$board}}
		        : {{$_code->libelleLong}}
	        {{/if}}
	        {{if $boardItem}}
		        : {{$_code->libelleLong|truncate:50:"...":false}}
	        {{/if}}
	        {{/foreach}}
	      {{/if}}
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
          {{if !$app->user_prefs.dPplanningOp_listeCompacte}}<br />{{/if}}
          {{$curr_op->time_operation|date_format:$dPconfig.time}}
        {{else}}
          Non validé
        {{/if}}
        <br />
        {{if $curr_op->horaire_voulu}}
        souhaité pour 
        {{if !$app->user_prefs.dPplanningOp_listeCompacte}}<br />{{/if}}
        {{$curr_op->horaire_voulu|date_format:$dPconfig.time}}
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
      {{include file=inc_documents_operation.tpl operation=$curr_op preloaded=true}}
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
      <a href="?m={{$m}}&amp;tab=w_edit_urgence&amp;operation_id={{$curr_op->_id}}" onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
      	{{if $curr_op->salle_id}}Déplacé en salle {{$curr_op->_ref_salle}}{{/if}}
        {{if $curr_op->libelle}}
          <em>[{{$curr_op->libelle}}]</em>
        {{/if}}

	    	{{if $app->user_prefs.dPplanningOp_listeCompacte}}
	        {{foreach from=$curr_op->_ext_codes_ccam item=_code name=codes}}
	        <strong>{{$_code->code}}</strong>
	        {{if !$smarty.foreach.codes.last}}&mdash;{{/if}}
	        {{/foreach}}
	      {{else}}
	        {{foreach from=$curr_op->_ext_codes_ccam item=_code}}
	        <br />
	        <strong>{{$_code->code}}</strong>
	        {{if !$board}}
		        : {{$_code->libelleLong}}
	        {{/if}}
	        {{if $boardItem}}
		        : {{$_code->libelleLong|truncate:50:"...":false}}
	        {{/if}}
	        {{/foreach}}
	      {{/if}}
      </a>
    </td>

    <td style="text-align: center;" {{if $curr_op->annulee}}class="cancelled"{{/if}}>
      {{if $curr_op->annulee}}
        [ANNULEE]
      {{else}}
      <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
        {{mb_value object=$curr_op field=_datetime}}
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
      {{include file=inc_documents_operation.tpl operation=$curr_op preloaded=true}}
    </td>
  </tr>
  {{/if}}

  {{/foreach}}
</table>
  