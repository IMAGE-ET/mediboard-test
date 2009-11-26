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
  
  {{foreach from=$curr_plage->_ref_operations item=_operation}}
  <tbody class="hoverable">
  <tr>
    {{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
    <td class="text top" {{if !$board}}rowspan="2"{{/if}}>
      <a href="{{$patient->_dossier_cabinet_url}}">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">{{$patient}}</strong>
      </a>
    </td>
    <td class="text top">
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}">
        {{mb_include template=inc_vw_operation}}
      </a>
    </td>
    {{if $_operation->annulee}}
    <td class="cancelled top" colspan="2">
      [ANNULEE]
    </td>
    {{else}}
    <td class="top" style="text-align: center;">
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}">
        {{if $_operation->time_operation != "00:00:00"}}
          Validé pour 
          {{if !$app->user_prefs.dPplanningOp_listeCompacte}}<br />{{/if}}
          {{$_operation->time_operation|date_format:$dPconfig.time}}
        {{else}}
          Non validé
        {{/if}}
        <br />
        {{if $_operation->horaire_voulu}}
        souhaité pour 
        {{if !$app->user_prefs.dPplanningOp_listeCompacte}}<br />{{/if}}
        {{$_operation->horaire_voulu|date_format:$dPconfig.time}}
        {{/if}}
      </a>
    </td>
    <td class="top" style="text-align: center;">
      <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}">
        {{$_operation->temp_operation|date_format:$dPconfig.time}}
      </a>
    </td>
    {{/if}}
  </tr>
  
  {{if !$board}}
  {{if !$_operation->annulee}}
	<tr>
    <td class="top" colspan="10">
      {{include file=inc_documents_operation.tpl operation=$_operation preloaded=true}}
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
  
  {{foreach from=$listUrgences item=_operation}}
  <tbody class="hoverable">
  <tr>
    {{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
    <td class="text" class="top" {{if !$board}}rowspan="2"{{/if}}>
      <a href="{{$patient->_dossier_cabinet_url}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient->_view}}
        </span>
      </a>
    </td>
    <td class="text top">
      <a href="?m={{$m}}&amp;tab=w_edit_urgence&amp;operation_id={{$_operation->_id}}">
      	{{if $_operation->salle_id}}Déplacé en salle {{$_operation->_ref_salle}}{{/if}}
        {{mb_include template=inc_vw_operation}}   
      </a>
    </td>
    <td class="top" style="text-align: center;" {{if $_operation->annulee}}class="cancelled"{{/if}}>
      {{if $_operation->annulee}}
        [ANNULEE]
      {{else}}
      <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$_operation->_id}}">
        {{mb_value object=$_operation field=_datetime}}
      </a>
      {{/if}}
    </td>
    <td class="top" style="text-align: center;">
      <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$_operation->_id}}">
        {{$_operation->temp_operation|date_format:$dPconfig.time}}
      </a>
    </td>
  </tr>

  {{if !$board}}
	<tr>
    <td class="top" colspan="10">
      {{include file=inc_documents_operation.tpl operation=$_operation preloaded=true}}
    </td>
  </tr>
  {{/if}}

  {{/foreach}}
</table>
  