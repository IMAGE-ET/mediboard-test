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

function submitConsultWithChronoExams(chrono) {
  var oForm = document.editFrmExams;
  oForm.chrono.value = chrono;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadMain });
}

function reloadMain() {
  var mainUrl = new Url;
  mainUrl.setModuleAction("dPcabinet", "httpreq_vw_main_consult");
  mainUrl.addParam("selConsult", document.editFrmExams.consultation_id.value);
  mainUrl.requestUpdate('mainConsult', { waitingText : null });
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
    <td id="listConsult" class="effectShown" style="vertical-align: top;">
    </td>
    {{if $consult->consultation_id}}
    <td class="greedyPane">
    {{else}}
    <td class="halfPane">
    {{/if}}

      {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      <form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
      <input type="hidden" name="_check_premiere" value="{{$consult->_check_premiere}}" />
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            <button id="triggerList" class="triggerHide" type="button" onclick="flipEffectElement('listConsult', 'Appear', 'Fade', 'triggerList');" style="float:left">+/-</button>
            <input type="hidden" name="chrono" value="{{$consult->chrono}}" />
            Consultation
            (Etat : {{$consult->_etat}}
            {{if $consult->chrono <= $smarty.const.CC_EN_COURS}}
            / 
            <button class="submit" type="button" onclick="submitConsultWithChronoExams({{$smarty.const.CC_TERMINE}})">Terminer</button>
            {{/if}})
          </th>
        </tr>
      </table>
      </form>

      
      {{include file="inc_accord_ant_consult.tpl"}}


      {{/if}}

    </td>
  </tr>
</table>
