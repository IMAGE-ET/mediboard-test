<style>
  button img{
    width:16px;
    height:16px;
  }
</style>
<table class="tbl">
  <tr>
    <th>Critère</th>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      <th class="text" style="width:13em;">{{$operation->_view}}</th>
    {{foreachelse}}
      <th style="width:13em;">Pas d'intervention</th>
    {{/foreach}}
  </tr>
  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank1');">
      <div class="rank">1</div><strong>Medecin anesthésiste</strong>
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
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank2');"><div class="rank">2</div><strong>Traitement habituel</strong></td>
    {{foreach from=$tab_op item=num_operation}}
      <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
    {{/foreach}}
  </tr>

  <tr>
    {{assign var="result" value=false}}
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque &&
      (($dm_sejour->_id &&
      ($dm_sejour->risque_antibioprophylaxie != "NR" || $dm_sejour->risque_prophylaxie != "NR" ||
      $dm_sejour->risque_MCJ_chirurgie != "NR" || $dm_sejour->risque_thrombo_chirurgie != "NR")
      || ($dm_patient->_id && ($dm_patient->facteurs_risque != "" ||
      $dm_patient->risque_thrombo_chirurgie != "NR" || $dm_patient->risque_MCJ_chirurgie != "NR"))))}}
      {{assign var="result" value=true}}
    {{/if}}

    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank3');"><div class="rank">3</div><strong>Risque anesthésique</strong></td>
    {{foreach from=$tab_op item=num_operation}}
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
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank4');"><div class="rank">4</div><strong>Type d'anesthésie</strong></td>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      <td class="button"><img src="images/icons/note_{{if $operation->type_anesth}}green{{else}}red{{/if}}.png"/></td>
    {{foreachelse}}
      <td class="button"><img src="images/icons/note_orange.png" title="N/A"/></td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank5');">
      <div class="rank">5</div><strong>Voies aériennes supérieures</strong>
    </td>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      {{assign var="result" value=false}}
      {{if ($operation->_ref_consult_anesth->mallampati && $operation->_ref_consult_anesth->bouche && $operation->_ref_consult_anesth->distThyro) || $operation->_ref_consult_anesth->conclusion}}
        {{assign var="result" value=true}}
      {{/if}}
      <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
    {{foreachelse}}
      {{assign var="result" value=false}}
      {{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
      {{if ($consult_anesth->mallampati && $consult_anesth->bouche && $consult_anesth->distThyro) || $consult_anesth->conclusion}}
        {{assign var="result" value=true}}
      {{/if}}
      <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank6');"><div class="rank">6</div><strong>Poids</strong></td>
    {{foreach from=$tab_op item=num_operation}}
      <td class="button"><img src="images/icons/note_{{if $constantes->poids}}green{{else}}red{{/if}}.png"/></td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank7');"><div class="rank">7</div><strong>Score ASA</strong></td>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      <td class="button"><img src="images/icons/note_{{if $operation->ASA}}green{{else}}red{{/if}}.png"/></td>
    {{foreachelse}}
      <td class="button"><img src="images/icons/note_orange.png" title="N/A"/></td>
    {{/foreach}}
  </tr>
  <tr>
    <td colspan="3" class="button">
      <button type="button" class="left" onclick="Control.Modal.close();">Continuer la consultation</button>
      <button type="button" class="save" onclick="document.formCheckConsultAnesth.submit();">Terminer la consultation</button>
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
    <tr><th class="title" colspan="2">Medecin anesthésiste</th></tr>
    <tr>
      <th>Critère</th>
      <td style="white-space: pre-wrap;">Identification du médecin anesthésiste sur le document traçant la phase pré-anesthésique</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Nom du médecin anesthésiste indiqué sur le document traçant la phase pré-anesthésique</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank2">
    <tr><th class="title" colspan="2">Traitement habituel</th></tr>
    <tr>
      <th>Critère</th>
      <td style="white-space: pre-wrap;">Mention du traitement habituel ou de l'absence de traitement dans le document traçant la CPA (si applicable)</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le document traçant la CPA indique formellement :
        - Soit l'existence et la mention du traitement habituel,
        - Soit l'absence de traitement.</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank3">
    <tr><th class="title" colspan="2">Risque anesthésique</th></tr>
    <tr>
      <th>Critère</th>
      <td style="white-space: pre-wrap;">Mention de l'évaluation du risque anesthésique dans le document traçant la CPA</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">La mention de l'évaluation du risque anesthésique est retrouvée dans le document traçant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank4">
    <tr><th class="title" colspan="2">Type d'anesthésie</th></tr>
    <tr>
      <th>Critère</th>
      <td style="white-space: pre-wrap;">Mention du type d'anesthésie proposé au patient dans le document traçant la CPA</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">La mention du type d'anesthésie proposé au patient est retrouvée dans le document traçant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank5">
    <tr><th class="title" colspan="2">Voies aériennes supérieures</th></tr>
    <tr>
      <th>Critère</th>
      <td style="white-space: pre-wrap;">Mention de l'évaluation des conditions d'abord des <strong>voies aériennes supérieures</strong> en phase pré-anesthésique dans le document traçant la CPA</td>
    </tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le score de Mallampati, la distance thyro-mentonnière ET l'ouverture de bouche sont retrouvés dans le document traçant la CPA
        OU<br/>Une conclusion explicite est retrouvée dans le document traçant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank6">
    <tr><th class="title" colspan="2">Poids</th></tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le poids est renseigné dans les constantes du patient</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank7">
    <tr><th class="title" colspan="2">Score ASA</th></tr>
    <tr>
      <th>Elemént requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le score ASA est renseigné dans l'intervention prévue</td>
    </tr>
  </table>
</div>