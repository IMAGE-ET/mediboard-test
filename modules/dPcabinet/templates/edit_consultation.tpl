{literal}
<script type="text/javascript">

function editPat(patient_id) {
  var url = new Url;
  url.setModuleTab("dPpatients", "vw_edit_patients");
  url.addParam("patient_id", patient_id);
  url.redirect();
}

function showAll(patient_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "vw_resume");
  url.addParam("dialog", 1);
  url.addParam("patient_id", patient_id);
  url.popup(800, 500, "Resume");
}

function newOperation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("operation_id", 0);
  url.redirect();
}

function newHospitalisation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_hospi");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("hospitalisation_id", 0);
  url.redirect();
}

function newConsultation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPcabinet", "edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("consultation_id", 0);
  url.redirect();
}

function pasteText(formName) {
  var form = document.editFrm;
  var aide = eval("form._aide_" + formName);
  var area = eval("form." + formName);
  insertAt(area, aide.value + '\n')
  aide.value = 0;
}

function pageMain() {

  {/literal}
  {if $consult->consultation_id}
  incPatientHistoryMain();
  initEffectClass("listConsult", "triggerList");
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
  listUpdater.periodicalUpdate('listConsult', { frequency: 60 });
  
}

</script>
{/literal}

<table class="main">
  <tr>
    <td id="listConsult" class="effectShown" style="vertical-align: top;">
    </td>
    {if $consult->consultation_id}
    <td>
    {else}
    <td class="halfPane">
    {/if}

      {if $consult->consultation_id}
      {assign var="patient" value=$consult->_ref_patient}

      <table class="form">
        <tr>
          <th class="category">
            <button id="triggerList" class="triggerHide" type="button" onclick="flipEffectElement('listConsult', 'Appear', 'Fade', 'triggerList');" style="float:left">+/-</button>
            Patient
          </th>
          <th class="category">Correspondants</th>
          <th class="category">
            <a style="float:right;" href="javascript:view_log('CConsultation',{$consult->consultation_id})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Historique
          </th>
          <th class="category">Planification</th>
        </tr>
        <tr>
          <td class="text">
            {include file="inc_patient_infos.tpl"}
          </td>
          <td class="text">
            {include file="inc_patient_medecins.tpl"}
          </td>
          <td class="text">
            {include file="inc_patient_history.tpl"}
          </td>
          <td class="button">
            <button style="margin: 1px;" class="new" type="button" onclick="newOperation      ({$consult->_ref_plageconsult->chir_id},{$consult->patient_id})">Nouvelle intervention</button>
            <br/>
            <button style="margin: 1px;" class="new" type="button" onclick="newHospitalisation({$consult->_ref_plageconsult->chir_id},{$consult->patient_id})">Nouveau séjour</button>
            <br/>
            <button style="margin: 1px;" class="new" type="button" onclick="newConsultation   ({$consult->_ref_plageconsult->chir_id},{$consult->patient_id})">Nouvelle consultation</button>
          </td>
        </tr>
      </table>

      <div id="mainConsult">
      {include file="inc_main_consultform.tpl"}
      </div>

      <div id="fdrConsult">
      {include file="inc_fdr_consult.tpl"}
      </div>

      {/if}

    </td>
  </tr>
</table>
