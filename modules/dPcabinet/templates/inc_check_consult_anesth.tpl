<style>
  button img{
    width:16px;
    height:16px;
  }
</style>
<table class="tbl">
  <tr>
    <th>Critère</th>
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
        {{if ($consult_anesth->mallampati && $consult_anesth->bouche && $consult_anesth->distThyro) || $consult_anesth->conclusion}}
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
  <tr>
    {{math equation="x+1" x=$tab_op|@count assign=colonnes}}
    <td colspan="{{$colonnes}}" class="button">
      <button type="button" class="undo" onclick="Control.Modal.close();">Continuer la consultation</button>
      <button type="button" class="tick" onclick="document.formCheckConsultAnesth.submit();">Terminer la consultation</button>
    </td>
  </tr>
</table>

<form class="watch" name="formCheckConsultAnesth" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  {{mb_key   object=$consult}}
  <input type="hidden" name="chrono" value="{{$consult|const:'TERMINE'}}" />
</form>

<div  style="display: none;">
  <table class="tbl" id="DetailRank1">
    <tr><th class="title" colspan="2">Identification du patient</th></tr>
    <tr>
      <th>Critère</th>
      <td>Identification du patient sur toutes les pièces du dossier</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Nom, prénom et date de naissance du patient indiqués sur les documents :
        - Traçant la consultation et la visite pré-anesthésique,
        - Phase per-anesthésique,
        - Post-interventionnelle.</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank2">
    <tr><th class="title" colspan="2">Medecin anesthésiste</th></tr>
    <tr>
      <th>Critère</th>
      <td>Identification du médecin anesthésiste sur le document traçant la phase pré-anesthésique</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td>Nom du médecin anesthésiste indiqué sur le document traçant la phase pré-anesthésique</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank4">
    <tr><th class="title" colspan="2">Traitement habituel</th></tr>
    <tr>
      <th>Critère</th>
      <td>Mention du traitement habituel ou de l'absence de traitement dans le document traçant la CPA (si applicable)</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le document traçant la CPA indique formellement :
        - Soit l'existence et la mention du traitement habituel,
        - Soit l'absence de traitement.</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank5">
    <tr><th class="title" colspan="2">Risque anesthésique</th></tr>
    <tr>
      <th>Critère</th>
      <td>Mention de l'évaluation du risque anesthésique dans le document traçant la CPA</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td>La mention de l'évaluation du risque anesthésique est retrouvée dans le document traçant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank6">
    <tr><th class="title" colspan="2">Type d'anesthésie</th></tr>
    <tr>
      <th>Critère</th>
      <td>Mention du type d'anesthésie proposé au patient dans le document traçant la CPA</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td>La mention du type d'anesthésie proposé au patient est retrouvée dans le document traçant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank7">
    <tr><th class="title" colspan="2">Voies aériennes supérieures</th></tr>
    <tr>
      <th>Critère</th>
      <td>Mention de l'évaluation des conditions d'abord des <strong>voies aériennes supérieures</strong> en phase pré-anesthésique dans le document traçant la CPA</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td>Le score de Mallampati, la distance thyro-mentonnière ET l'ouverture de bouche sont retrouvés dans le document traçant la CPA
        <br/>OU<br/>Une conclusion explicite est retrouvée dans le document traçant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRankPoids">
    <tr><th class="title" colspan="2">Poids</th></tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td>Le poids est renseigné dans les constantes du patient</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRankASA">
    <tr><th class="title" colspan="2">Score ASA</th></tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td>Le score ASA est renseigné dans l'intervention prévue</td>
    </tr>
  </table>
</div>