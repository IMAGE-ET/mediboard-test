{{if !$board}}
<script>
  Main.add(function(){
    Calendar.regField(getForm("changeDate").date, null, {noView: true});
  });

  synchronizeView = function(form) {
    var canceled = $V(form._canceled) ? 1 : 0;
    $V(form.canceled, canceled);
    form.submit();
  }
</script>

<form action="?" name="changeDate" method="get" style="font-weight:bold; padding: 2px; text-align:center; display: block;">
  {{$date|date_format:$conf.longdate}}
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_idx_planning" />
  <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
  <div style="float: right;">
    Afficher les interventions annulées ({{$nb_canceled}})
    <input type="checkbox" name="_canceled" value="1" {{if $canceled}}checked="checked"{{/if}} onchange="synchronizeView(this.form)" />
    <input type="hidden" name ="canceled" value="{{$canceled}}" />
  </div>
</form>
{{/if}}

<script>
  ObjectTooltip.modes.allergies = {  
    module: "patients",
    action: "ajax_vw_allergies",
    sClass: "tooltip"
  };

  printPlanningChir = function(date, prat_id) {
    var url = new Url("dPbloc", "view_planning");
    url.addParam("_datetime_min", date + " 00:00:00");
    url.addParam("_datetime_max", date + " 23:59:59");
    url.addParam("_prat_id", prat_id);
    url.pop('800', '600');
  }
</script>

<table class="tbl" {{if $board}}style="font-size: 9px;"{{/if}}>
  <tr>
    <th id="didac_th_interv" class="title" colspan="3">
      <button type="button" style="float: right;" class="notext print" onclick="printPlanningChir('{{$date}}', '{{$praticien->_id}}');">{{tr}}Print{{/tr}}</button>
      Interventions
    </th>
  </tr>
  
  <tr>
    <th id="didac_th_listing_interv"></th>
    <th id="didac_th_listing_patient">{{mb_label class=CSejour field=patient_id}}</th>
    <th>
      [{{mb_label class=COperation field=libelle}}] 
      {{mb_label class=COperation field=codes_ccam}}
    </th>
  </tr>
  
  {{foreach from=$listDay item=_plage}}
  <tr>
    <th class="section">
      {{mb_include module=system template=inc_object_notes object=$_plage}}
    </th>
    <th colspan="2" class="section">
      {{if $g != $_plage->_ref_salle->_ref_bloc->group_id}}
        <span style="font-size: 1.2em">{{$_plage->_ref_salle->_ref_bloc->_ref_group}}</span><br/>
      {{/if}}
      {{$_plage->_ref_salle}} : 
      {{mb_include module=system template=inc_interval_time from=$_plage->debut to=$_plage->fin}}
    </th>
  </tr>
  
  {{assign var=prev_interv value=null}}
  {{assign var=real_prev_interv value=null}}
  {{assign var=prev_prev_interv_id value=-1}}
  
  {{assign var=list_operations value=$_plage->_ref_operations|@array_values}}
  {{foreach from=$list_operations item=_operation name=_operation}}
    <tbody class="hoverable">
      <tr>
        {{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
        {{assign var=background value=""}}
        {{if $_operation->entree_salle && $_operation->sortie_salle}}
          {{assign var=background value="background-image:url(images/icons/ray.gif); background-repeat:repeat;"}}
        {{elseif $_operation->entree_salle}}
          {{assign var=background value="background-color:#cfc;"}}
        {{elseif $_operation->sortie_salle}}
          {{assign var=background value="background-color:#fcc;"}}
        {{elseif $_operation->entree_bloc}}
          {{assign var=background value="background-color:#ffa;"}}
        {{/if}}
        <td rowspan="2" class="narrow {{if $_operation->annulee}}cancelled{{/if}}" style="text-align: center; {{$background}}">
          {{if !$_operation->annulee}}
          {{if !$board && !$_operation->rank && (!$prev_interv || $prev_interv && !$prev_interv->rank) && !($_plage->spec_id && !$_plage->unique_chir)}}
            {{if $_operation->rank_voulu || $_operation->horaire_voulu}}
              {{if !$smarty.foreach._operation.first && $prev_interv && !$prev_interv->rank}}
                <form name="move-{{$_operation->_guid}}-up" action="?m={{$m}}" method="post" class="prepared" style="display: block;"
                      onsubmit="return onSubmitFormAjax(this, updateListOperations.curry('{{$date}}'))">
                  <input type="hidden" name="m" value="dPplanningOp" />
                  <input type="hidden" name="dosql" value="do_planning_aed" />
                  <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
                  <input type="hidden" name="_place_after_interv_id" value="{{$prev_prev_interv_id}}" />
                  <button type="submit" class="up notext oneclick" title="{{tr}}Up{{/tr}}"></button>
                </form>
              {{/if}}
            {{else}}
              <form name="place-{{$_operation->_guid}}" action="?m={{$m}}" method="post" class="prepared" style="display: block;"
                    onsubmit="return onSubmitFormAjax(this, updateListOperations.curry('{{$date}}'))">
                <input type="hidden" name="m" value="dPplanningOp" />
                <input type="hidden" name="dosql" value="do_planning_aed" />
                <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
                <input type="hidden" name="_place_after_interv_id" value="{{if $real_prev_interv}}{{$real_prev_interv->_id}}{{else}}-1{{/if}}" />
                <button type="submit" class="tick notext oneclick" title="Placer"></button>
              </form>
            {{/if}}
          {{/if}}

          {{if $_operation->rank}}
            <div class="rank">{{$_operation->rank}}</div>
          {{elseif $_operation->rank_voulu}}
            <div class="rank desired" title="Pas encore validé par le bloc">{{$_operation->rank_voulu}}</div>
          {{else}}
            <div class="rank opacity-20"></div>
          {{/if}}

          {{if !$board && !$_operation->rank && !$smarty.foreach._operation.last && !($_plage->spec_id && !$_plage->unique_chir)}}
            {{assign var=next_index value=$smarty.foreach._operation.iteration}}
            {{assign var=next_interv value=$list_operations.$next_index}}

            {{if $_operation->rank_voulu || $_operation->horaire_voulu}}
              {{if $prev_interv}}
                {{assign var=prev_prev_interv_id value=$prev_interv->_id}}
              {{/if}}
              {{assign var=prev_interv value=$_operation}}

              {{if $next_interv->rank_voulu || $next_interv->horaire_voulu}}
                <form name="move-{{$_operation->_guid}}-down" action="?m={{$m}}" method="post" class="prepared" style="display: block;"
                      onsubmit="return onSubmitFormAjax(this, updateListOperations.curry('{{$date}}'))">
                  <input type="hidden" name="m" value="dPplanningOp" />
                  <input type="hidden" name="dosql" value="do_planning_aed" />
                  <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
                  <input type="hidden" name="_place_after_interv_id" value="{{$next_interv->_id}}" />
                  <button type="submit" class="down notext oneclick" title="{{tr}}Down{{/tr}}"></button>
                </form>
              {{/if}}
            {{/if}}
          {{/if}}
          {{/if}}
        </td>

        {{if $_operation->rank_voulu || $_operation->horaire_voulu}}
          {{assign var=prev_interv value=$_operation}}
        {{/if}}

        <td class="text top" {{if !$board}}rowspan="2"{{/if}}>
          {{if $patient->_ref_dossier_medical->_id && $patient->_ref_dossier_medical->_count_allergies}}
            <img src="images/icons/warning.png" style="float: right" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" />
          {{/if}}
          {{assign var=prescription value=$_operation->_ref_sejour->_ref_prescription_sejour}}
          {{if $prescription && $prescription->_id && $prescription->_counts_by_chapitre|@array_sum}}
            <img src="images/icons/ampoule_blue.png" style="float: right;" title="{{$prescription->_counts_by_chapitre|@array_sum}} ligne(s) prescrite(s) par le praticien" />
          {{/if}}
          {{if $_operation->annulee}}
            [ANNULEE]
          {{else}}
            <strong>
              {{if $_operation->time_operation != "00:00:00"}}
                {{mb_value object=$_operation field=time_operation}}
              {{elseif $_operation->horaire_voulu}}
                {{mb_value object=$_operation field=horaire_voulu}}
              {{/if}}
            </strong>
          {{/if}}

          <a href="{{$patient->_dossier_cabinet_url}}">
            <strong
              class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}}"
              onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
              {{$patient}}
            </strong>
          </a>
          {{mb_label object=$_operation field=temp_operation}} : {{mb_value object=$_operation field=temp_operation}}
          <br/>{{$_operation->_ref_sejour->type|truncate:1:""|capitalize}}
        </td>
        <td class="text top">
          <button type="button" class="injection" style="float: right;" onclick="Operation.dossierBloc('{{$_operation->_id}}', updateListOperations)">
            Dossier bloc
          </button>
          <a href="#1" onclick="Operation.editModal('{{$_operation->_id}}', '{{$_operation->plageop_id}}', updateListOperations)" style="float: left;">
            {{mb_include template=inc_vw_operation}}
            ({{mb_label object=$_operation field=cote}} {{mb_value object=$_operation field=cote}})
          </a>

          {{assign var=commande value=$_operation->_ref_commande_mat}}
          {{if $commande && $commande->_id}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$commande->_guid}}')" style="float: left;">
&nbsp;&nbsp;{{tr}}COperation-materiel-court{{/tr}} {{mb_value object=$commande field=etat}}
              </span>
          {{/if}}
        </td>
      </tr>

      {{if !$board && !$_operation->annulee}}
        <tr>
          <td class="top" colspan="3">
            {{mb_include module=dPplanningOp template=inc_documents_operation operation=$_operation preloaded=true}}
          </td>
        </tr>
      {{/if}}

    </tbody>
  
  {{assign var=real_prev_interv value=$_operation}}
  {{foreachelse}}
  <tr><td colspan="3" class="empty">Aucune intervention dans cette plage</td></tr>
  {{/foreach}}
 
  {{foreachelse}}
  <tr><td colspan="3" class="empty">Aucune plage ce jour</td></tr>
  {{/foreach}}
  
  {{if $listUrgences|@count}}
  <tr>
    <th colspan="10" class="section">Hors plage</th>
  </tr>
  {{/if}}
  
  {{foreach from=$listUrgences item=_operation}}
  <tbody class="hoverable">
    <tr>
      {{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
      
      {{assign var=background value=""}}
      {{if $_operation->entree_salle && $_operation->sortie_salle}}
        {{assign var=background value="background-image:url(images/icons/ray.gif); background-repeat:repeat;"}}
      {{elseif $_operation->entree_salle}}
        {{assign var=background value="background-color:#cfc;"}}
      {{elseif $_operation->sortie_salle}}
        {{assign var=background value="background-color:#fcc;"}}
      {{elseif $_operation->entree_bloc}}
        {{assign var=background value="background-color:#ffa;"}}
      {{/if}}
      <td rowspan="2" class="narrow" style="{{$background}}"></td>
      
      <td class="top text" class="top" {{if !$board}}rowspan="2"{{/if}}>
        {{assign var=prescription value=$_operation->_ref_sejour->_ref_prescription_sejour}}
        {{if $prescription && $prescription->_id && $prescription->_counts_by_chapitre|@array_sum}}
          <img src="images/icons/ampoule_blue.png" style="float: right;" />
        {{/if}}
        {{if $patient->_ref_dossier_medical->_id && $patient->_ref_dossier_medical->_count_allergies}}
          <img src="images/icons/warning.png" style="float: right" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" />
        {{/if}}

        {{if $_operation->annulee}}
          [ANNULEE]
        {{else}}
          <strong>
            {{mb_value object=$_operation field=time_operation}}
          </strong>
        {{/if}}
        
        <a href="{{$patient->_dossier_cabinet_url}}">
          <strong
            class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">{{$patient}}</strong>
        </a>
        {{mb_label object=$_operation field=temp_operation}} : {{mb_value object=$_operation field=temp_operation}}
        <br/>{{$_operation->_ref_sejour->type|truncate:1:""|capitalize}}
      </td>
      <td class="text top">
        <button type="button" class="injection" style="float: right;" onclick="Operation.dossierBloc('{{$_operation->_id}}', updateListOperations.curry('{{$date}}'))">
          Dossier bloc
        </button>
        <a href="#1" onclick="Operation.editModal('{{$_operation->_id}}', '{{$_operation->plageop_id}}', updateListOperations)" style="float: left;">
          {{if $_operation->salle_id}}Effectué en salle {{$_operation->_ref_salle}}{{/if}}
          {{mb_include template=inc_vw_operation}}
          ({{mb_label object=$_operation field=cote}} {{mb_value object=$_operation field=cote}})  
        </a>
        {{assign var=commande value=$_operation->_ref_commande_mat}}
        {{if $commande && $commande->_id}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$commande->_guid}}')" style="float: left;">
&nbsp;&nbsp;{{tr}}COperation-materiel-court{{/tr}} {{mb_value object=$commande field=etat}}
              </span>
        {{/if}}
      </td>
    </tr>
  
    {{if !$board}}
    <tr>
      <td class="top" colspan="3">
        {{mb_include template=inc_documents_operation operation=$_operation preloaded=true}}
      </td>
    </tr>
    {{/if}}
  </tbody>
  {{/foreach}}
</table>
  