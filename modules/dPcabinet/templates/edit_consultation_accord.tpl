<script type="text/javascript">
function verifNonEmpty(oElement){
  var notWhitespace = /\S/;
  if(notWhitespace.test(oElement.value)){
    return true;
  }
  return false;
}

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

function refreshListActesNGAP(){
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_actes_ngap");
  url.addParam("consultation_id", "{{$consult->_id}}");
  url.requestUpdate('listActesNGAP');
}

function deleteActeNGAP(acte_ngap_id){
  var oForm = document.editNGAP;
  oForm.del.value = 1;
  oForm.acte_ngap_id.value = acte_ngap_id;
  submitFormAjax(oForm, 'systemMsg', { onComplete: refreshListActesNGAP } );
}



function pageMain() {
  updateList();
  
  PairEffect.initGroup("acteEffect");
  
  {{if $consult->consultation_id}}
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
  {{/if}}
  
  if (document.editAntFrm){
    document.editAntFrm.type.onchange();
    
    {{if $dPconfig.dPcabinet.addictions}}
      Try.these(document.editAddictFrm.type.onchange);
    {{/if}}
  } 
   
  {{if $consult->consultation_id}}
  
  // Chargement des antecedents, traitements, addictions, diagnostics du patients
  reloadDossierMedicalPatient();
  
  var oAccord = new Rico.Accordion( $('accordionConsult'), { 
    panelHeight: ViewPort.SetAccordHeight('accordionConsult' ,{ iBottomMargin : 10 } ),
    showDelay:50, 
    showSteps:5
  } );
  
  new Control.Tabs('main_tab_group'); 
  {{/if}}
 
}
</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    <td class="greedyPane">
			{{include file="../../dPpatients/templates/inc_intermax.tpl" debug=true}}
			
      {{if $consult->_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      <div id="finishBanner">
      {{include file="inc_finish_banner.tpl"}}
      </div>
      {{include file="inc_patient_infos_accord_consult.tpl"}}
      {{include file="acc_consultation.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>
