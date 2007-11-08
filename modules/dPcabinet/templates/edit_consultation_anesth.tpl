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

function submitForm(oForm) {
  submitFormAjax(oForm, 'systemMsg');
}

function submitOpConsult() {
  var oForm = document.addOpFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : editSejourId}); 
}


// Fonction permettant de garder un champ sejour_id toujours a la bonne valeur
// Le champs sejour_id mis à jour est dans le form addOpFrm dans intervention.tpl
function editSejourId(){
  var sejourUrl = new Url;
  sejourUrl.setModuleAction("dPcabinet","httpreq_vw_sejour_anesth");
  sejourUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  sejourUrl.requestUpdate('sejour_id', { waitingText:null, onComplete : reloadConsultAnesth});
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
  
  // Mise a jour du champ _sejour_id pour la creation d'antecedent, de traitement et d'addiction
  document.editTrmtFrm._sejour_id.value = document.addOpFrm.sejour_id.value;
  document.editAntFrm._sejour_id.value = document.addOpFrm.sejour_id.value;
  document.editAddictFrm._sejour_id.value = document.addOpFrm.sejour_id.value;
  document.editDiagAnesthFrm.object_id.value = document.addOpFrm.sejour_id.value;
  
  // Mise a jour du champ listCim10 du sejour
  document.editDiagAnesthFrm.listCim10.value = document.addOpFrm.listCim10Sejour.value;
  
  // refresh de la liste des antecedents du sejour
  reloadAntecedentsAnesth();
   
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

  // Mise a jour du champ listCim10 du sejour
 
  document.editDiagAnesthFrm.listCim10.value = document.addOpFrm.listCim10Sejour.value;

 
  //rafraichissement de la liste des consultations
  updateList();  

  // Chargement des antecedents, traitements, addictions, diagnostics du patients
  reloadAntecedents();
  
  // Chargement des antecedents, traitements, addictions, diagnostics du sejour
  {{if $_is_anesth}}
  reloadAntecedentsAnesth();
  {{/if}}

  PairEffect.initGroup("acteEffect");
  
  {{if $consult->consultation_id}}
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
  {{/if}}
  
    
  if (document.editAntFrm) {
    document.editAntFrm.type.onchange();

    {{if $dPconfig.dPcabinet.addictions}}
    Try.these(document.editAddictFrm.type.onchange);
    {{/if}}
    oCimField = new TokenField(document.editDiagFrm.listCim10, { 
      confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
      onChange : updateTokenCim10
    } );
  
    oCimAnesthField = new TokenField(document.editDiagAnesthFrm.listCim10, { 
      confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
      onChange : updateTokenCim10Anesth
    } );
  }
  
 
  // Accordeon
  {{if $consult->consultation_id}}
  var oAccord = new Rico.Accordion( $('accordionConsult'), { 
    panelHeight: ViewPort.SetAccordHeight('accordionConsult', { sOtherElmt: 'finishBanner', iBottomMargin : 12, iMinHeight : 360 } ), 
    showDelay:50, 
    showSteps:3 
  } );
  {{/if}}




}

</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    <td class="greedyPane" id="tdConsultation">
    
    {{include file="../../dPpatients/templates/inc_intermax.tpl" debug=true}}
    
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