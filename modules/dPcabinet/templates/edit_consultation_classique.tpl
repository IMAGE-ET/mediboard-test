<script type="text/javascript">
function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_select_docs"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
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

function updateList() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");

  url.addParam("selConsult", "{{$consult->consultation_id}}");
  url.addParam("prat_id", "{{$userSel->user_id}}");
  url.addParam("date", "{{$date}}");
  url.addParam("vue2", "{{$vue}}");

  url.periodicalUpdate('listConsult', { frequency: 90 });
}

function pageMain() {
  updateList();
  
  {{if $consult->consultation_id}}
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  {{/if}}
}

</script>


<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;">
    </td>
    <td class="greedyPane" id="tdConsultation">
			{{include file="../../dPpatients/templates/inc_intermax.tpl"}}
      {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      <div id="finishBanner">
      {{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
      </div>

      <div id="Infos">
        <div id="InfosContent"  class="accordionTabContentBox">
          {{include file="../../dPcabinet/templates/inc_patient_infos_accord_consult.tpl"}}
        </div>
      </div>

      <div id="mainConsult">
      {{include file="../../dPcabinet/templates/inc_main_consultform.tpl"}}
      </div>

      <div id="fdrConsultContent">
      {{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}
      </div>

      {{/if}}

    </td>
  </tr>
</table>
