{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription"}}
  {{mb_script module="dPprescription" script="prescription_editor"}}
  {{mb_script module="dPprescription" script="element_selector"}}
{{/if}}

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="edit_consultation"}}

<script type="text/javascript">

tabSejour = {{$tabSejour|@json}};

{{if !$consult->_canEdit}}
  App.readonly = true;
{{/if}}

printFiche = function() {
  var url = new Url("dPcabinet", "print_fiche"); 
  url.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
  url.addParam("print", true);
  url.popup(700, 500, "printFiche");
};

reloadConsultAnesth = function() {
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
  var consultUrl = new Url("dPcabinet", "httpreq_vw_consult_anesth");
  consultUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  consultUrl.requestUpdate('consultAnesth');
  
  // Reload Infos Anesth
  var infosAnesthUrl = new Url("dPcabinet", "httpreq_vw_choix_anesth");
  infosAnesthUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  infosAnesthUrl.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
  infosAnesthUrl.requestUpdate('InfoAnesth');
 
  Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null,'', null, false);

  if($('facteursRisque')){
    refreshFacteursRisque();
  }
};

submitAll = function() {
  var oForm;
  oForm = getForm("editFrmIntubation");
  if(oForm) {
    onSubmitFormAjax(oForm);
  }
  oForm = getForm("editExamCompFrm");
  if(oForm) {
    onSubmitFormAjax(oForm);
  }
  oForm = getForm("editFrmExams");
  if(oForm) {
    onSubmitFormAjax(oForm);
  }
};

submitOpConsult = function() {
  onSubmitFormAjax(getForm("addOpFrm"), { onComplete: reloadConsultAnesth } ); 
};

reloadDiagnostic = function(sejour_id, modeDAS) {
  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim");
};

view_history_consult = function(id) {
  var url = new Url("dPcabinet", "vw_history");
  url.addParam("consultation_id", id);
  url.popup(600, 500, "consult_history");
};

submitForm = function(oForm) {
  onSubmitFormAjax(oForm);
};

printAllDocs = function() {
  var url = new Url('cabinet', 'print_select_docs');
  url.addElement(document.editFrmFinish.consultation_id);
  if(DossierMedical.sejour_id) {
    url.addParam("sejour_id", DossierMedical.sejour_id);
  }
  url.popup(700, 500, "printDocuments");
};

</script>

{{if $consult->_id}}
  {{assign var="patient" value=$consult->_ref_patient}}
  {{if $consult_anesth}}
    {{include file="../../dPcabinet/templates/inc_consult_anesth/patient_infos.tpl"}}
  {{/if}}
  <div id="finishBanner">
    {{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
  </div>
  {{if $consult_anesth}}
    {{include file="../../dPcabinet/templates/inc_consult_anesth/accord.tpl"}}
  {{else}}
    {{include file="../../dPcabinet/templates/inc_patient_infos_accord_consult.tpl"}}
    {{include file="../../dPcabinet/templates/acc_consultation.tpl"}}
  {{/if}}
{{/if}}

    