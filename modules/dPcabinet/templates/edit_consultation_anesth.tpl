{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="prescription_editor"}}
{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim", { waitingText : null } );
}

var tabSejour = {{$tabSejour|@json}};

function view_history_consult(id){
  url = new Url();
  url.setModuleAction("dPcabinet", "vw_history");
  url.addParam("consultation_id", id);
  url.popup(600, 500, "consult_history");
}

function submitForm(oForm) {
  submitFormAjax(oForm, 'systemMsg');
}

function submitOpConsult() {
  var oForm = document.addOpFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete: reloadConsultAnesth } ); 
}

function submitAll() {
  var oForm = document.forms['edit-constantes-medicales'];
  submitFormAjax(oForm, 'systemMsg');
  
  oForm = document.editFrmIntubation;
  submitFormAjax(oForm, 'systemMsg');
  
  oForm = document.editExamCompFrm;
  submitFormAjax(oForm, 'systemMsg');  
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
  url.addParam("sejour_id", DossierMedical.sejour_id);
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
  var sejour_id = tabSejour[document.addOpFrm.operation_id.value];
  if (!sejour_id) {
    sejour_id = document.addOpFrm.sejour_id.value;
  }
  
  // Mise a jour du sejour_id
  DossierMedical.updateSejourId(sejour_id);
  
  // refresh de la liste des antecedents du sejour
  DossierMedical.reloadDossierPatient();
  DossierMedical.reloadDossierSejour();
  

  // Reload Intervention
  var consultUrl = new Url;
  consultUrl.setModuleAction("dPcabinet", "httpreq_vw_consult_anesth");
  consultUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  consultUrl.requestUpdate('consultAnesth', { waitingText: null } );
  
  // Reload Infos Anesth
  var infosAnesthUrl = new Url;
  infosAnesthUrl.setModuleAction("dPcabinet", "httpreq_vw_choix_anesth");
  infosAnesthUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  infosAnesthUrl.requestUpdate('InfoAnesth', { waitingText: null } );
 
  Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null, true, !Preferences.mode_readonly,'');
}

Main.add(function () {

  //rafraichissement de la liste des consultations
  updateList();  
  
  // Chargement pour le sejour
  DossierMedical.reloadDossierSejour();
  
  {{if $consult->consultation_id}}
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  {{/if}}

  if (document.editAntFrm) {
    document.editAntFrm.type.onchange();
  }
  
  {{if $consult->_id}}
  // Chargement des antecedents, traitements, diagnostics du patients
  DossierMedical.reloadDossierPatient();
  {{/if}}
});

</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    <td class="greedyPane" id="tdConsultation">
    
    {{include file="../../dPpatients/templates/inc_intermax.tpl"}}
    
    {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      {{include file="../../dPcabinet/templates/inc_consult_anesth/patient_infos.tpl"}}
      <div id="finishBanner">
      {{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
      </div>
      {{include file="../../dPcabinet/templates/inc_consult_anesth/accord.tpl"}}
    {{/if}}

    </td>
  </tr>
</table>