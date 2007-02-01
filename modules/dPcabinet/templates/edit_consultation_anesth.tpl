<script type="text/javascript">
function verifNonEmpty(oElement){
  var notWhitespace = /\S/;
  if(notWhitespace.test(oElement.value)){
    return true;
  }
  return false;
}
function view_history_consult(id){
  url = new Url();
  url.setModuleAction("dPcabinet", "vw_history");
  url.addParam("consultation_id", id);
  url.popup(600, 500, "consult_history");
}

var oCookie = new CJL_CookieUtil("AnesthAccordion");
var fHeight = 340;
if(oCookie.getSubValue("height")){
  var fHeight = oCookie.getSubValue("height");
}

function initAccord(init_resize){
  var oAccordionDiv = $("accordionConsult");
  if (!oAccordionDiv) {
    return;
  }
  var fHeightDivTitle = 0;
  var fhauteur_div = 0;
  
  // Cumul des AccordTitle
  fHeightDivTitle = Element.getOffsetHeightByClassName("accordionTabTitleBar");
  
  // Position Top des infos patient
  fposTop_tdConsultAnesth = Position.cumulativeOffset($("tdConsultation"))[1];
  
  // Position Top de l'accord
  fposTop_div = Position.cumulativeOffset(oAccordionDiv)[1]; 
  
  fhauteur_div = window.getInnerDimensions().x - (fposTop_div - fposTop_tdConsultAnesth) - fHeightDivTitle;
  aAccordBorderTop = Element.getStyle("accordionConsult","border-top-width").split("px");
  fHeight = fhauteur_div - parseFloat(aAccordBorderTop[0]) - 14;
  //-14 pour les marges et bordures en bas des tableaux
  if(fHeight<=340){
    fHeight = 340;
  }
  oCookie.setSubValue("height", fHeight);
  if(init_resize){
    oAccord.lastExpandedTab.content.style.height = fHeight + "px";
    oAccord.options.panelHeight = fHeight;
  }
}















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
  url.popup(700, 500, "printFiche");
  return;
}

function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_select_docs"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
  return;
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



var oCimField = null;
var oCimAnesthField = null;

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
  
  initAccord(true);
  
  oCimField = new TokenField(document.editDiagFrm.listCim10, { 
    confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
    onChange : updateTokenCim10
    } );
  
  oCimAnesthField = new TokenField(document.editTabacFrm.listCim10, { 
    confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
    onChange : updateTokenCim10Anesth
    } );
}

</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    {{if $consult->consultation_id}}
    <td class="greedyPane" id="tdConsultation">
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

