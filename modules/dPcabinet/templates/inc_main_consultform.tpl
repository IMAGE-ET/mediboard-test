{literal}
<script type="text/javascript">

function submitConsultWithChrono(chrono) {
  var oForm = document.editFrm;
  oForm.chrono.value = chrono;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadMain });
}

function reloadMain() {
  var mainUrl = new Url;
  mainUrl.setModuleAction("dPcabinet", "httpreq_vw_main_consult");
  mainUrl.addParam("selConsult", document.editFrm.consultation_id.value);
  mainUrl.requestUpdate('mainConsult', { waitingText : null });
}

</script>
{/literal}

<form class="watch" name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm(this);">

<input type="hidden" name="m" value="{$m}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
<input type="hidden" name="consultation_id" value="{$consult->consultation_id}" />
<input type="hidden" name="_check_premiere" value="{$consult->_check_premiere}" />

<table class="form">
  <tr>
    <th class="category" colspan="4">
      <input type="hidden" name="chrono" value="{$consult->chrono}" />
      Consultation
      (Etat : {$consult->_etat}
      {if $consult->chrono <= $smarty.const.CC_EN_COURS}
      / 
      <button class="submit" type="button" onclick="submitConsultWithChrono({$smarty.const.CC_TERMINE})">Terminer</button>
      {/if})
    </th>
  </tr>
  <tr>
    <th class="category">
      <label for="motif" title="Motif de la consultation">Motif</label>
    </th>
    <th>
      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {html_options options=$consult->_aides.motif}
      </select>
    </th>
    <th class="category">
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
    </th>
    <th>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {html_options options=$consult->_aides.rques}
      </select>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2"><textarea name="motif" rows="5">{$consult->motif}</textarea></td>
    <td class="text" colspan="2"><textarea name="rques" rows="5">{$consult->rques}</textarea></td>
  </tr>
  <tr>
    <th class="category">
      <label for="examen" title="Bilan de l'examen clinique">Examens</label>
    </th>
    <th>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {html_options options=$consult->_aides.examen}
      </select>
    </th>
    <th class="category">
      <label for="traitement" title="title">Traitements</label>
    </th>
    <th>
      <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {html_options options=$consult->_aides.traitement}
      </select>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2"><textarea name="examen" rows="5">{$consult->examen}</textarea></td>
    <td class="text" colspan="2"><textarea name="traitement" rows="5">{$consult->traitement}</textarea></td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
        sauver
      </button>
    </td>
  </tr>
</table>

</form>