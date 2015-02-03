{{assign var=need_more_atc value=0}}

{{if $conf.dPcabinet.CConsultAnesth.check_close}}
  <style>
    button img{
      width:16px;
      height:16px;
    }

  </style>

<table class="main layout">
  <tr>
    <td>
      <fieldset>
        <legend>IPAQSS</legend>
        <table class="tbl">
          <tr>
            <th id="didac_th_Critere">Critère</th>
            {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth}}
              {{if $consult_anesth->_ref_operation->_id}}
                <th class="text" style="width:13em;">{{$consult_anesth->_ref_operation->_view}}</th>
              {{else}}
                <th style="width:13em;">Pas d'intervention</th>
              {{/if}}
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank1');">
              <div class="rank">1</div><strong>Identification du patient</strong>
            </td>
            {{foreach from=$tab_op item=num_operation}}
              <td class="button"><img src="images/icons/note_green.png"/></td>
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank2');">
              <div class="rank">2</div><strong>Medecin anesthésiste</strong>
            </td>
            {{foreach from=$tab_op item=num_operation}}
              <td class="button"><img src="images/icons/note_green.png"/></td>
            {{/foreach}}
          </tr>
          <tr>
            {{assign var="result" value=false}}
            {{if $dm_patient->absence_traitement || $dm_patient->_ref_traitements|@count ||
            ($dm_patient->_ref_prescription && $dm_patient->_ref_prescription->_ref_prescription_lines|@count)}}
              {{assign var="result" value=true}}
            {{/if}}
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank4');"><div class="rank">4</div><strong>Traitement habituel</strong></td>
            {{foreach from=$tab_op item=num_operation}}
              <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank5');"><div class="rank">5</div><strong>Risque anesthésique</strong></td>
            {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth}}
              {{assign var="operation_id" value=$consult_anesth->operation_id}}
              {{assign var="dm_sejour" value=false}}

              {{if $operation_id}}
                {{assign var="dm_sejour" value=$consult_anesth->_ref_operation->_ref_sejour->_ref_dossier_medical}}
              {{/if}}

              {{assign var="result" value=false}}
              {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque &&
              ((($dm_sejour && $dm_sejour->_id &&
              ($dm_sejour->risque_antibioprophylaxie != "NR" || $dm_sejour->risque_prophylaxie != "NR" ||
              $dm_sejour->risque_MCJ_chirurgie != "NR" || $dm_sejour->risque_thrombo_chirurgie != "NR"))
              || ($dm_patient->_id && ($dm_patient->facteurs_risque != "" ||
              $dm_patient->risque_thrombo_patient != "NR" || $dm_patient->risque_MCJ_patient != "NR"))))}}
                {{assign var="result" value=true}}
              {{/if}}

              <td class="button">
                {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
                  <img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/>
                {{else}}
                  <img src="images/icons/note_orange.png" title="N/A"/>
                {{/if}}
              </td>
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank6');"><div class="rank">6</div><strong>Type d'anesthésie</strong></td>
            {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth}}
              {{if $consult_anesth->_ref_operation->_id}}
                <td class="button"><img src="images/icons/note_{{if $consult_anesth->_ref_operation->type_anesth}}green{{else}}red{{/if}}.png"/></td>
              {{else}}
                <td class="button"><img src="images/icons/note_orange.png" title="N/A"/></td>
              {{/if}}
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank7');">
              <div class="rank">7</div><strong>Voies aériennes supérieures</strong>
            </td>
            {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth}}
              {{assign var="result" value=false}}
              {{if ($consult_anesth->mallampati && $consult_anesth->bouche && $consult_anesth->distThyro && $consult_anesth->mob_cervicale) || $consult_anesth->conclusion}}
                {{assign var="result" value=true}}
              {{/if}}
              <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRankPoids');"><div class="rank"></div><strong>Poids</strong></td>
            {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth}}
              {{assign var="result" value=false}}
              {{if $consult_anesth->_ref_consultation->_ref_patient->_ref_constantes_medicales->poids}}
                {{assign var="result" value=true}}
              {{/if}}
              <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
            {{/foreach}}
          </tr>
          <tr>
            <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRankASA');"><div class="rank"></div><strong>Score ASA</strong></td>
            {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth}}
              {{if $consult_anesth->_ref_operation->_id}}
                <td class="button"><img src="images/icons/note_{{if $consult_anesth->_ref_operation->ASA}}green{{else}}red{{/if}}.png"/></td>
              {{else}}
                <td class="button"><img src="images/icons/note_orange.png" title="N/A"/></td>
              {{/if}}
            {{/foreach}}
          </tr>
          {{else}}
          <table class="tbl">
            <tr>
              <td colspan="2" style="text-align: center;">Voulez-vous vraiment terminer la consultation?</td>
            </tr>
            {{/if}}
          </table>
      </fieldset>
    </td>
    {{if $conf.dPpatients.CAntecedent.mandatory_types}}
      <td>
        <fieldset>
          <legend>Antécédents obligatoires</legend>
            <table class="tbl">
              {{foreach from=$mandatory_types key=_type item=_atc}}
                <tr>
                  <td>
                    <strong>{{tr}}CAntecedent.type.{{$_type}}{{/tr}}</strong>
                  </td>
                  <td style="text-align: center;">
                    <img src="images/icons/note_{{if $_atc|@count}}green{{else}}red{{/if}}.png"/>
                  </td>
                </tr>
              {{/foreach}}
            </table>

        </fieldset>
      </td>
    {{/if}}
  </tr>
</table>

<p style="text-align: center">
  {{math equation="x+1" x=$tab_op|@count assign=colonnes}}
    <button type="button" class="undo" onclick="Control.Modal.close();">Continuer la consultation</button>

    {{if $consult->chrono <= $consult|const:'EN_COURS' && !$need_more_atc}}
      <button type="button" class="tick" onclick="document.formCheckConsultAnesth.submit();">Terminer la consultation</button>
      {{if $consult->_refs_dossiers_anesth|@count > 1 || $op_sans_dossier_anesth}}
        <br/>
        <button type="button" id="didac_button_terminer_gerer_suivants" class="edit" onclick="onSubmitFormAjax(document.formCheckConsultAnesth, { onComplete : function () {Control.Modal.close();GestionDA.edit();} } );">
          Terminer la consultation et gérer les dossiers suivants
        </button>
      {{/if}}
    {{elseif $consult->_refs_dossiers_anesth|@count > 1 || $op_sans_dossier_anesth}}
      <button type="button" class="edit" onclick="Control.Modal.close();GestionDA.edit();">
        Gérer les dossiers suivants
      </button>
    {{/if}}
</p>

<form class="watch" name="formCheckConsultAnesth" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  {{mb_key   object=$consult}}
  <input type="hidden" name="chrono" value="{{$consult|const:'TERMINE'}}" />
</form>

{{mb_include module=cabinet template=vw_legend_check_anesth}}