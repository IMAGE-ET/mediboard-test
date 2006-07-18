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
  var oForm = document.editFrmExams;
  var aide = eval("oForm._aide_" + formName);
  var area = eval("oForm." + formName);
  insertAt(area, aide.value + '\n')
  aide.value = 0;
}

function submitAll() {
  var oForm = document.editFrmExams;
  submitFormAjax(oForm, 'systemMsg');
}

function pageMain() {

  {{if $consult->consultation_id}}
  incPatientHistoryMain();
  initEffectClass("listConsult", "triggerList");
  {{/if}}
  
  var listUpdater = new Url;
  listUpdater.setModuleAction("dPcabinet", "httpreq_vw_list_consult");

  listUpdater.addParam("selConsult", "{{$consult->consultation_id}}");
  listUpdater.addParam("prat_id", "{{$userSel->user_id}}");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.addParam("vue2", "{{$vue}}");

  listUpdater.periodicalUpdate('listConsult', { frequency: 60 });
  
}

</script>


<table class="main">
  <tr>
    <td id="listConsult" class="effectShown" style="vertical-align: top;">
    </td>
    {{if $consult->consultation_id}}
    <td>
    {{else}}
    <td class="halfPane">
    {{/if}}

      {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      <div id="finishBanner">
      {{include file="inc_finish_banner.tpl"}}
      </div>

      <div id="Infos">
        <div id="InfosContent"  class="accordionTabContentBox">
          {{include file="inc_patient_infos_accord_consult.tpl"}}
        </div>
      </div>

      <div id="mainConsult">
      {{include file="inc_main_consultform.tpl"}}
      </div>

      <div id="fdrConsultContent">
      {{include file="inc_fdr_consult.tpl"}}
      </div>

      {{/if}}

    </td>
  </tr>
</table>
