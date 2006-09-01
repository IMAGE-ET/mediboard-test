<script type="text/javascript">

function submitForm(oForm) {
  submitFormAjax(oForm, 'systemMsg');
}

function submitOpConsult() {
  var oForm = document.addOpFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadConsultAnesth});
}

function submitAll() {
  var oForm = document.editAnesthPatFrm;
  submitFormAjax(oForm, 'systemMsg');
  var oForm1 = document.editFrmIntubation;
  submitFormAjax(oForm1, 'systemMsg');
  var oForm2 = document.editExamCompFrm;
  submitFormAjax(oForm2, 'systemMsg');  
}

function showAll(patient_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "vw_resume");
  url.addParam("dialog", 1);
  url.addParam("patient_id", patient_id);
  url.popup(800, 500, "Resume");
}

function printFiche() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, url, "printFiche");
  return;
}

function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_docs"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, url, "printDocuments");
  return;
}

function updateList() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
  url.addParam("selConsult", "{{$consult->consultation_id}}");
  url.addParam("prat_id", "{{$userSel->user_id}}");
  url.addParam("date", "{{$date}}");
  url.addParam("vue2", "{{$vue}}");
  url.periodicalUpdate('listConsult', { frequency: 60 });
}

function reloadConsultAnesth() {
  // Reload Intervention
  var consultUrl = new Url;
  consultUrl.setModuleAction("dPcabinet", "httpreq_vw_consult_anesth");
  consultUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  consultUrl.requestUpdate('consultAnesth');
  
  // Reload Infos Anesth
  var infosAnesthUrl = new Url;
  infosAnesthUrl.setModuleAction("dPcabinet", "httpreq_vw_choix_anesth");
  infosAnesthUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  infosAnesthUrl.requestUpdate('InfoAnesthContent');
}





function pageMain() {
  updateList();

  {{if $consult->consultation_id}}
  incPatientHistoryMain();
  incAntecedantsMain();
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
  {{/if}}
}

</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    {{if $consult->consultation_id}}
    <td class="greedyPane">
    {{else}}
    <td class="halfPane">
    {{/if}}
    
    {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      {{include file="inc_consult_anesth/patient_infos.tpl"}}      
      {{include file="inc_consult_anesth/accord.tpl"}}
      <div id="finishBanner">
      {{include file="inc_finish_banner.tpl"}}
      </div>
    {{/if}}

    </td>
  </tr>
</table>

