<script type="text/javascript">

function printFiche() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addElement(document.editFrm.consultation_id);
  url.popup(700, 500, url, "printFiche");
  return;
}


function showAll(patient_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "vw_resume");
  url.addParam("dialog", 1);
  url.addParam("patient_id", patient_id);
  url.popup(800, 500, "Resume");
}

function pasteText(formName) {
  var form = document.editFrm;
  var aide = eval("form._aide_" + formName);
  var area = eval("form." + formName);
  insertAt(area, aide.value + '\n')
  aide.value = 0;
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
  var anesthUrl = new Url;
  anesthUrl.setModuleAction("dPcabinet", "httpreq_vw_choix_anesth");
  anesthUrl.addParam("selConsult", document.editFrm.consultation_id.value);
  anesthUrl.requestUpdate('choixAnesth');
}

function submitAll() {
  var oForm = document.editFrmExams;
  submitFormAjax(oForm, 'systemMsg');
  var oForm1 = document.editFrmIntubation;
  submitFormAjax(oForm1, 'systemMsg');
}

function pageMain() {
    

  {{if $consult->consultation_id}}
  incPatientHistoryMain();
  incAntecedantsMain();
  initEffectClass("listConsult", "triggerList");
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
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
    <td id="listConsult" style="vertical-align: top;">
    </td>
    {{if $consult->consultation_id}}
    <td class="greedyPane">
    {{else}}
    <td class="halfPane">
    {{/if}}
    
      {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      
      <div id="finishBanner">
      {{include file="inc_finish_banner.tpl"}}
      </div>
      
      {{include file="inc_accord_ant_consultAnesth.tpl"}}

    {{/if}}

    </td>
  </tr>
</table>

