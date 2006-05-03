<script type="text/javascript">
{literal}

function printFiche() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addElement(document.editFrm.consultation_id);
  url.popup(700, 500, url, "printFiche");
  return;
}

function editPat(patient_id) {
  var url = new Url;
  url.setModuleAction("dPpatients", "vw_edit_patients");
  url.addParam("patient_id", patient_id);
  url.redirect();
}

function pasteText(formName) {
  var form = document.editFrm;
  var aide = eval("form._aide_" + formName);
  var area = eval("form." + formName);
  insertAt(area, aide.value + '\n')
  aide.value = 0;
}

function submitConsultWithChrono(chrono) {
  var form = document.editFrm;
  form.chrono.value = chrono;
  form.submit();
}

function submitConsultAnesth() {
  var oForm = document.editAnesthPatFrm;
  submitFormAjax(oForm, 'systemMsg');
}

function submitOpConsult() {
  var oForm = document.addOpFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadConsultAnesth});
}

function reloadConsultAnesth() {
  var consultUrl = new Url;
  consultUrl.setModuleAction("dPcabinet", "httpreq_vw_consult_anesth");
  consultUrl.addParam("selConsult", document.editFrm.consultation_id.value);
  consultUrl.requestUpdate('consultAnesth');
}

function pageMain() {
    
  {/literal}
  {if $consult->consultation_id}
  incPatientHistoryMain();
  incAntecedantsMain();
  initEffectClass("listConsult", "triggerList");
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
  {/if}
  {literal}
  
  var listUpdater = new Url;
  listUpdater.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
  {/literal}
  listUpdater.addParam("selConsult", "{$consult->consultation_id}");
  listUpdater.addParam("prat_id", "{$userSel->user_id}");
  listUpdater.addParam("date", "{$date}");
  listUpdater.addParam("vue2", "{$vue}");
  {literal}
  listUpdater.periodicalUpdate('listConsult', { frequency: 30 });
  
}

{/literal}
</script>

<table class="main">
  <tr>
    <td id="listConsult" style="vertical-align: top">
    </td>
    {if $consult->consultation_id}
    <td>
    {else}
    <td class="halfPane">
    {/if}
    
      {if $consult->consultation_id}
      {assign var="patient" value=$consult->_ref_patient}

      <form name="editFrm" action="?m={$m}" method="post">

      <input type="hidden" name="m" value="{$m}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="consultation_id" value="{$consult->consultation_id}" />
      <input type="hidden" name="_check_premiere" value="{$consult->_check_premiere}" />

      <table class="form">
        <tr>
          <th class="category" colspan="4">
            <button type="button" style="float: left;" onclick="printFiche()">
              <img src="modules/dPcabinet/images/print.png" alt="historique" />
              Imprimer la fiche
              <img src="modules/dPcabinet/images/print.png" alt="historique" />
            </button>
            <input type="hidden" name="chrono" value="{$consult->chrono}" />
            Consultation
            (Etat : {$consult->_etat}
            {if $consult->chrono <= $smarty.const.CC_EN_COURS}
            / <input type="button" value="Terminer" onclick="submitConsultWithChrono({$smarty.const.CC_TERMINE})" />
            {/if})
          </th>
        </tr>
      </table>

      </form>

      <table class="form">
        <tr>
          <th class="category">
            <button id="triggerList" class="triggerHide" type="button" onclick="flipEffectElement('listConsult', 'Appear', 'Fade', 'triggerList');" style="float:left">+/-</button>
            Patient
          </th>
          <th class="category">Informations</th>
          <th class="category">Correpondants</th>
          <th class="category">
            <a style="float:right;" href="javascript:view_log('CConsultation',{$consult->consultation_id})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Historique
          </th>
        </tr>
        <tr>
          <td class="text">
            {include file="inc_patient_infos.tpl"}
          </td>
          <td class="text" id="consultAnesth">
          {include file="inc_vw_consult_anesth.tpl"}
          </td>
          <td class="text">
            {include file="inc_patient_medecins.tpl"}
          </td>
          <td class="text">
            {include file="inc_patient_history.tpl"}
          </td>
        </tr>
      </table>

      {include file="inc_ant_consult.tpl"}

      {if $consult_anesth->consultation_anesth_id}
      <table class="form">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>
        <tr>
          <td class="text">
            Intervention le <strong>{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}</strong>
            par le <strong>Dr. {$consult_anesth->_ref_operation->_ref_chir->_view}</strong><br />
            <ul>
              {foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}
              <li><em>{$curr_code->libelleLong}</em> ({$curr_code->code})</li>
              {/foreach}
            </ul>
          </td>
          <td class="text">
            <form name="editOpFrm" action="?m=dPcabinet" method="post">

            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{$consult_anesth->_ref_operation->operation_id}" />
            <label for="type_anesth" title="Type d'anesthésie pour l'intervention">Type d'anesthésie :</label>
            <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg')">
              <option value="">&mdash; Choisir un type d'anesthésie</option>
              {html_options options=$anesth selected=$consult_anesth->_ref_operation->type_anesth}
            </select>

            </form>
          </td>
        </tr>
      </table>
      {/if}

      <div id="fdrConsult">
      {include file="inc_fdr_consult.tpl"}
      </div>
    {/if}

    </td>
  </tr>
</table>

