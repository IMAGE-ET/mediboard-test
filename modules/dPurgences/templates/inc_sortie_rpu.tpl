{{assign var=sejour_id value=$sejour->_id}}

{{assign var=rpu value=$sejour->_ref_rpu}}
{{assign var=rpu_id value=$rpu->_id}}

{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=atu value=$sejour->_ref_consult_atu}}

{{mb_script module=dPadmissions script=admissions ajax=true}}

{{* La consultation de l'urgentiste peut être sur le séjour reliquat *}}
{{if $rpu->mutation_sejour_id && $rpu->mutation_sejour_id != $rpu->sejour_id}}
  {{assign var=atu value=$rpu->_ref_sejour_mutation->_ref_consult_atu}}
{{/if}}

{{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
{{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}

<td class="text {{if $sejour->annule}} cancelled {{/if}}" colspan="2">

  <form name="validCotation-{{$atu->_id}}" action="" method="post" class="prepared"> 
    <input type="hidden" name="dosql" value="do_consultation_aed" />
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="consultation_id" value="{{$atu->_id}}" />
    <input type="hidden" name="valide" value="1" />    
  </form>

  {{mb_include template=inc_rpu_patient}}
</td>

{{if $sejour->annule}}
<td class="cancelled" colspan="10">
  {{if $rpu->mutation_sejour_id}}
  Hospitalisation
  <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
    dossier {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$rpu->_ref_sejour_mutation}}
  </a> 
  {{else}}
  {{tr}}Cancelled{{/tr}}
  {{/if}}
</td>

{{else}}
{{if $conf.dPurgences.responsable_rpu_view}}
<td>
  <a href="{{$rpu_link}}">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
  </a>
</td>
{{/if}}

<td class="button {{if $sejour->type != "urg" && !$sejour->UHCD}} arretee {{/if}}">
  {{include file="inc_pec_praticien.tpl"}}
</td>

<td class="text">
  <button class="search notext" style="float: right;" onclick="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
    {{tr}}Info{{/tr}}
  </button>
  {{if $is_praticien || $access_pmsi}}
    <button class="edit notext" style="float: right" onclick="editFieldsRpu('{{$rpu_id}}');"></button>
  {{/if}}

  <!-- Vérification des champs semi obligatoires -->
  {{if "CAppUI::conf"|static_call:"dPurgences Display check_ccmu":"CGroups-$g" !== "0"}}
    {{if !$rpu->ccmu           }}<div class="warning">Champ manquant {{mb_label object=$rpu field=ccmu           }}</div>{{/if}}
  {{/if}}

  {{if "CAppUI::conf"|static_call:"dPurgences Display check_dp":"CGroups-$g" !== "0"}}
    {{if !$sejour->DP          }}<div class="warning">Champ manquant {{mb_label object=$sejour field=DP          }}</div>{{/if}}
  {{/if}}

  {{if "CAppUI::conf"|static_call:"dPurgences Display check_gemsa":"CGroups-$g" !== "0"}}
    {{if !$rpu->gemsa          }}<div class="warning">Champ manquant {{mb_label object=$rpu field=gemsa          }}</div>{{/if}}
  {{/if}}

  {{if "CAppUI::conf"|static_call:"dPurgences Display check_cotation":"CGroups-$g" !== "0"}}
    {{if (!$rpu->_ref_consult->_ref_actes && !$rpu->mutation_sejour_id) ||
      ($rpu->mutation_sejour_id && !$rpu->_ref_sejour_mutation->_count_actes)}}
      <div class="warning">Codage des actes manquant</div>
    {{/if}}
    {{if $sejour->sortie_reelle && !$rpu->_ref_consult->valide}}<div class="warning">La cotation n'est pas validée</div>{{/if}}
  {{/if}}

  {{if "CAppUI::conf"|static_call:"dPurgences CRPU display_motif_sfmu":"CGroups-$g" && "CAppUI::conf"|static_call:"dPurgences Display check_gemsa":"CGroups-$g" !== "0"}}
    {{if !$rpu->motif_sfmu    }}<div class="warning">Champ manquant {{mb_label object=$rpu field=motif_sfmu      }}</div>{{/if}}
  {{/if}}

  {{if $conf.dPurgences.old_rpu == "1"}}
  {{if !$rpu->type_pathologie}}<div class="warning">Champ manquant {{mb_label object=$rpu field=type_pathologie}}</div>{{/if}}
  {{if !$rpu->urtrau         }}<div class="warning">Champ manquant {{mb_label object=$rpu field=urtrau         }}</div>{{/if}}
  {{if !$rpu->urmuta         }}<div class="warning">Champ manquant {{mb_label object=$rpu field=urmuta         }}</div>{{/if}}

  {{/if}}

  {{if $sejour->sortie_reelle}}
     {{if $sejour->destination}}
       <strong>{{mb_label object=$sejour field=destination}}</strong> :
       {{mb_value object=$sejour field=destination}} <br />
     {{/if}}
     {{if $rpu->orientation}}
       <strong>{{mb_label object=$rpu field=orientation}}</strong> :
       {{mb_value object=$rpu field=orientation}}      
     {{/if}}
  {{/if}}
</td>

<td class="text sortie {{$sejour->mode_sortie}}">
  <span style="float: right;">
    {{if $sejour->UHCD}}
      <span class="encart encart-uhcd">UHCD</span>
    {{/if}}

    {{if $rpu->mutation_sejour_id}}
      <span class="texticon texticon-mutation">Muta</span>
    {{/if}}

    {{if $sejour->_ref_curr_affectation &&
         $sejour->_ref_curr_affectation->_ref_service &&
         $sejour->_ref_curr_affectation->_ref_service->radiologie}}
      <span class="encart encart-imagerie">IMG</span>
    {{/if}}
  </span>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
    {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
    {{if !$sejour->sortie_reelle}} 
      {{mb_title object=$sejour field=_entree}}
    {{/if}}
    <strong>
      {{mb_value object=$sejour field=entree date=$date}}
      {{if $sejour->sortie_reelle}} 
      &gt; {{mb_value object=$sejour field=sortie date=$date}}
      {{/if}}
    </strong>

  </span>

  <br style="clear: both;"/>
  {{if $rpu->mutation_sejour_id && $sejour->mode_sortie != "mutation"}}
    <div class="warning">
      Un séjour de mutation a été détecté, mais le mode de sortie <strong>mutation</strong> n'a pas été renseigné.
    </div>
  {{/if}}

  {{if $can->admin && $rpu->mutation_sejour_id && $sejour->_id != $rpu->mutation_sejour_id}}
    <form method="post" name="annulerHospitalisation">
      <input type="hidden" name="m" value="urgences"/>
      <input type="hidden" name="dosql" value="do_cancel_hospitalization_aed"/>
      <input type="hidden" name="sejour_guid" value="{{$sejour->_guid}}"/>
      <button type="submit" class="unlink notext" title="{{tr}}cancel-hospitalization{{/tr}}" style="float: right;">
        {{tr}}cancel-hospitalization{{/tr}}
      </button>
    </form>
  {{/if}}

  {{if $sejour->sortie_reelle || $sejour->mode_sortie == "mutation"}}
    {{if "ecap"|module_active}}
      <button class="ecap notext singleclick" style="float: right;" onclick="DHE.closeDHE('{{$sejour->_id}}')">
        {{tr}}Close{{/tr}}
      </button>
    {{/if}}

    <button class="edit notext" style="float: right;" onclick="refreshExecuter.stop(); Admissions.validerSortie('{{$sejour->_id}}', false, (function () {refreshExecuter.resume(); Sortie.refresh('{{$rpu->_id}}')}).bind(refreshExecuter), refreshExecuter.resume.bind(refreshExecuter))">
      {{tr}}Edit{{/tr}} {{mb_label object=$sejour field=sortie}}
    </button>

    {{mb_title object=$sejour field=sortie}} :
    {{mb_value object=$sejour field=mode_sortie}}

    {{if $sejour->mode_sortie == "transfert" && $sejour->etablissement_sortie_id}}
      <br />&gt; <strong>{{mb_value object=$sejour field=etablissement_sortie_id}}</strong>
    {{/if}}

    {{if $sejour->mode_sortie == "mutation" && $sejour->service_sortie_id}}
      {{assign var=service_id value=$sejour->service_sortie_id}}
      {{assign var=service value=$services.$service_id}}
      <br />Vers: <strong>{{$service}}</strong>
      {{foreach from=$rpu->_ref_sejour_mutation->_ref_affectations item=_affectation}}
        &rarr; <strong>{{$_affectation}}</strong>
      {{/foreach}}
    {{/if}}

    <div class="compact">{{mb_value object=$sejour field=commentaires_sortie}}</div>

  {{else}}
    {{if $sejour->mode_sortie != "normal"}}
      <div class="warning">
        Le mode de sortie est 
        <strong>
          {{mb_value object=$sejour field=mode_sortie}}
        </strong>
        mais la sortie réelle n'est pas validée
      </div>
    {{/if}}
    <button class="tick" onclick="refreshExecuter.stop(); Admissions.validerSortie('{{$sejour->_id}}', false, (function () {refreshExecuter.resume(); Sortie.refresh('{{$rpu->_id}}')}).bind(refreshExecuter), refreshExecuter.resume.bind(refreshExecuter))">
      {{tr}}Validate{{/tr}} {{mb_label object=$sejour field=sortie}}
    </button>
  {{/if}}
  </td>

  {{if "CAppUI::conf"|static_call:"dPurgences Display check_can_leave":"CGroups-$g" !== "0"}}
    {{if $sejour->mode_sortie == "mutation"}}
      <td></td>
    {{else}}
      <td id="rpu-{{$rpu->_id}}" style="font-weight: bold" class="text {{if !$rpu->sortie_autorisee}}arretee{{/if}} {{$rpu->_can_leave_level}}">
        {{if $sejour->sortie_reelle}}
          {{if !$rpu->sortie_autorisee}}
            {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
          {{/if}}
        {{elseif $rpu->_can_leave == -1}}
          {{if $sejour->type != "urg"}}
            {{mb_value object=$sejour field=type}}<br />
          {{elseif !$atu->_id}}
            Pas encore de prise en charge<br />
          {{else}}
            {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.48{{/tr}} <br />
          {{/if}}
          {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
        {{elseif $rpu->_can_leave != -1 && !$rpu->sortie_autorisee}}
          {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.64{{/tr}} <br />
          {{tr}}CRPU-sortie_assuree.0{{/tr}}
        {{else}}
          {{if $rpu->_can_leave_since}}
            {{tr}}CRPU-_can_leave_since{{/tr}}
          {{/if}}
          {{if $rpu->_can_leave_about}}
            {{tr}}CRPU-_can_leave_about{{/tr}}
          {{/if}}
          <span title="{{$sejour->sortie_prevue}}">{{mb_value object=$rpu field="_can_leave"}}</span><br />
          {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
        {{/if}}
      </td>
    {{/if}}
  {{/if}}

{{/if}}
