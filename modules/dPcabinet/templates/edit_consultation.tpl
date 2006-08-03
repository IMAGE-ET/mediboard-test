<script type="text/javascript">

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

  url.periodicalUpdate('listConsult', { frequency: 60 });
}

function pageMain() {
  updateList();

  {{if $consult->consultation_id}}
  incPatientHistoryMain();
  incAntecedantsMain();
  initEffectClassPlus("listConsult", "triggerList", { sEffect : "appear", bStartVisible : true });
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
      <div id="finishBanner">
      {{include file="inc_finish_banner.tpl"}}
      </div>
      
      {{include file="inc_accord_ant_consult.tpl"}}

      {{/if}}

    </td>
  </tr>
</table>
