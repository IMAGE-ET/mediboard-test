{{mb_default var=lite_view value=false}}
{{mb_default var=show_full_affectation value=false}}
{{mb_default var=default_tab value=""}}
{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{mb_default var=prescription value=$sejour->_ref_prescription_sejour}}

<script>
  function paramUserSejour(sejour_id) {
    var url = new Url("planningOp", "vw_affectations_sejour");
    url.addParam("sejour_id",sejour_id);
    url.requestModal(null, null, {
      onClose : function() {
        refreshLineSejour(sejour_id);
      }});
  }
</script>

{{if ($service_id && $service_id != "NP") || $show_affectation || $function->_id || $praticien->_id}}
  {{assign var=affectation value=$sejour->_ref_curr_affectation}}
  <td class="text {{if $sejour->isolement}}isolement{{/if}} {{if !$affectation->_id}}compact{{/if}}">
    {{if @$modules.dPplanningOp->_can->admin}}
      <button class="mediuser_black notext" onclick="paramUserSejour({{$affectation->sejour_id}});"
              onmouseover="ObjectTooltip.createDOM(this, 'affectation_{{$sejour->_guid}}')";
        {{if $sejour->_ref_users_sejour|@count == 0}}style="opacity: 0.6;" {{/if}}></button>
      {{if $sejour->_ref_users_sejour|@count}}
        <span class="countertip">{{$sejour->_ref_users_sejour|@count}}</span>
      {{/if}}
      {{mb_include module=planningOp template=vw_user_sejour_table}}
    {{/if}}

    {{if $affectation->_id && $affectation->lit_id}}
      {{if $show_full_affectation}}
        {{$affectation->_ref_lit->_view}}
      {{else}}
        {{mb_value object=$affectation->_ref_lit field=nom}}
      {{/if}}
    {{elseif $sejour->_ref_next_affectation->_id && $sejour->_ref_next_affectation->lit_id}}
      {{if $show_full_affectation}}
        {{$sejour->_ref_next_affectation->_ref_lit->_view}}
      {{else}}
        {{mb_value object=$sejour->_ref_next_affectation->_ref_lit field=nom}}
      {{/if}}
    {{/if}}
  </td>
{{/if}}

<td class="narrow">
  {{mb_include module=patients template=inc_vw_photo_identite size=32 nodebug=true}}
</td>

<td class="text">
  {{if $lite_view && "pharmacie Display show_risq_population"|conf:"CGroups-$g"}}
    <span class="compact" style="float:right">
      {{if $patient->naissance}}
        {{if $patient->_annees <= "pharmacie Risque_pop age_min"|conf:"CGroups-$g"}}
          < {{"pharmacie Risque_pop age_min"|conf:"CGroups-$g"}} ans
        {{elseif $patient->_annees >= "pharmacie Risque_pop age_max"|conf:"CGroups-$g"}}
          > {{"pharmacie Risque_pop age_max"|conf:"CGroups-$g"}} ans
        {{/if}}
      {{/if}}
      {{if $patient->_ref_last_grossesse && $patient->_ref_last_grossesse->terme_prevu >= $smarty.now}}
        <img onmouseover="ObjectTooltip.createEx(this, '{{$patient->_ref_last_grossesse->_guid}}')"
             src="style/mediboard/images/icons/grossesse.png" style="background-color: rgb(255, 215, 247);"/>
      {{/if}}

      {{assign var=score_asa value="pharmacie Risque_pop score_asa"|conf:"CGroups-$g"}}
      {{if $score_asa}}
        {{foreach from=$sejour->_ref_operations item=_interv}}
          {{if $_interv->ASA >= $score_asa}}
            ASA &ge; {{$score_asa}}
          {{/if}}
        {{/foreach}}
      {{/if}}
    </span>
  {{/if}}

  {{assign var=statut value="present"}}

  {{if $sejour->septique}}
    {{assign var=statut value="septique"}}
  {{/if}}

  {{if !$sejour->entree_reelle || ($sejour->_ref_prev_affectation->_id && $sejour->_ref_prev_affectation->effectue == 0)}}
    {{assign var=statut value="attente"}}
  {{/if}}

  {{if $sejour->sortie_reelle || $sejour->_ref_curr_affectation->effectue == 1}}
    {{assign var=statut value="sorti"}}
  {{/if}}

  {{mb_include module=ssr template=inc_view_patient statut=$statut onclick="showDossierSoins('`$sejour->_id`','$date', '$default_tab');"}}
</td>


{{if "dPImeds"|module_active}}
<td>
    <span onclick="showDossierSoins('{{$sejour->_id}}','{{$date}}','Imeds');">
    {{mb_include module=Imeds template=inc_sejour_labo link="#"}}
    </span>
</td>
{{/if}}

{{if !$lite_view}}
<td style="text-align: center;">
  {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
    {{mb_include module=system template=inc_icon_alerts
       object=$prescription
       callback="function() { refreshLineSejour('`$sejour->_id`')}"
       nb_alerts=$prescription->_count_alertes}}
  {{else}}
    {{if $sejour->_ref_prescription_sejour->_count_fast_recent_modif}}
      <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
      {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_ref_prescription_sejour->_count_fast_recent_modif}}
    {{/if}}
  {{/if}}
</td>
<td style="text-align: center;">
  {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
    {{mb_include module=system template=inc_icon_alerts
       object=$prescription
       callback="function() { refreshLineSejour('`$sejour->_id`')}"
       nb_alerts=$prescription->_count_urgences
       level="high"}}
  {{/if}}
</td>
<td style="text-align: center;">
  {{if $sejour->_count_tasks}}
    <img src="images/icons/phone_orange.png" onclick="showTasks(this, 'tooltip-content-tasks-{{$sejour->_id}}', '{{$sejour->_id}}');"
      onmouseover="this.style.cursor='pointer';"/>
    {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_count_tasks}}

    <div id="tooltip-content-tasks-{{$sejour->_id}}" style="display: none; height: 400px; width: 400px:"></div>
  {{/if}}

  {{if $sejour->_count_tasks_not_created}}
    <img src="images/icons/phone_red.png" onclick="showTasksNotCreated(this, 'tooltip-content-tasks-not-created-{{$sejour->_id}}', '{{$sejour->_id}}');"
         onmouseover="this.style.cursor='pointer';"/>
    {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_count_tasks_not_created}}

    <div id="tooltip-content-tasks-not-created-{{$sejour->_id}}" style="display: none; height: 400px; width: 400px:"></div>
  {{/if}}
</td>
{{/if}}

<td style="text-align: center;">
  {{if $dossier_medical && $dossier_medical->_id && $dossier_medical->_count_allergies}}
    <img src="images/icons/warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_ref_patient->_guid}}', 'allergies');" />
    {{mb_include module=system template=inc_vw_counter_tip count=$dossier_medical->_count_allergies}}
  {{/if}}
</td>
<td style="text-align: center;">
  {{if $dossier_medical && $dossier_medical->_count_antecedents && ($dossier_medical->_count_antecedents > $dossier_medical->_count_allergies)}}
    {{if $sejour->_ref_dossier_medical && $sejour->_ref_dossier_medical->_id}}
      {{assign var=dossier_medical value=$sejour->_ref_dossier_medical}}
    {{/if}}
    <span class="texticon texticon-atcd" onmouseover="ObjectTooltip.createEx(this, '{{$dossier_medical->_guid}}', 'antecedents');">Atcd</span>
  {{/if}}
</td>
<td>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
    {{mb_value object=$sejour field=entree format=$conf.date}}
  </span>

  <div style="position: relative">
    <div class="ecap-sejour-bar" title="arrivée il y a {{$sejour->_entree_relative}}j et départ prévu dans {{$sejour->_sortie_relative}}j ({{mb_value object=$sejour field=sortie}})">
      {{assign var=progress_bar_width value=0}}
      {{if $sejour->_duree}}
        {{math assign=progress_bar_width equation='100*(-entree / (duree))' entree=$sejour->_entree_relative duree=$sejour->_duree format='%.2f'}}
      {{/if}}

      <div style="width: {{if $sejour->_duree && $progress_bar_width <= 100}}{{$progress_bar_width}}{{else}}100{{/if}}%;"></div>
    </div>
  </div>
</td>
<td class="text">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
    {{mb_value object=$sejour field=_motif_complet}}
  </span>
  {{if $prescription->_id}}
    {{foreach from=$prescription->_jour_op item=_info_jour_op}}
      <br />
      (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
    {{/foreach}}
  {{/if}}
</td>
<td>
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien initials=border}}
</td>

{{if !$lite_view}}
<td class="text compact">
  {{foreach from=$sejour->_ref_transmissions item=_transmission}}
    <div onmouseover="ObjectTooltip.createEx(this, '{{$_transmission->_guid}}')">
      <strong>{{$_transmission->type|substr:0:1|upper}}</strong>:{{$_transmission->text}}
    </div>
  {{/foreach}}
</td>
{{/if}}