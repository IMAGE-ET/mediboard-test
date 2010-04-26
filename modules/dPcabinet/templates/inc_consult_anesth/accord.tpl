{{assign var="chir_id" value=$consult->_ref_plageconsult->_ref_chir->_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}

{{mb_include module=dPsalleOp template=js_codage_ccam}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

{{if $isPrescriptionInstalled && $dPconfig.dPcabinet.CPrescription.view_prescription}}
function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'', null, false);
}
{{/if}}

var constantesMedicalesDrawn = false;
function refreshConstantesMedicales (force) {
  if (!constantesMedicalesDrawn || force) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("patient_id", {{$consult->_ref_patient->_id}});
    url.addParam("context_guid", "{{$consult->_guid}}");
    url.requestUpdate("Constantes");
    constantesMedicalesDrawn = true;
  }
}

refreshFacteursRisque = function(){
  var url = new Url("dPcabinet", "httpreq_vw_facteurs_risque");
	url.addParam("consultation_id", "{{$consult->_id}}");
	url.requestUpdate("facteursRisque");
}

Main.add(function () {
  tabsConsultAnesth = new Control.Tabs.create('tab-consult-anesth', false);
  {{if $app->user_prefs.ccam_consultation == 1}}
  var tabsActes = Control.Tabs.create('tab-actes', false);
  {{/if}}
});
</script>

<!-- Tab titles -->
<ul id="tab-consult-anesth" class="control_tabs">
  <li onmousedown="DossierMedical.reloadDossierSejour();"><a href="#AntTrait">Antécédents</a></li>
  <li onmousedown="refreshConstantesMedicales();"><a href="#Constantes">Constantes</a></li>
  <li><a href="#Exams">Exam. Clinique</a></li>
  <li><a href="#Intub">Intubation</a></li>
  {{if $app->user_prefs.ccam_consultation == 1}}
  <li><a href="#Actes">Actes</a></li>
  {{/if}}
  <li><a href="#ExamsComp">Exam. Comp.</a></li>
  <li><a href="#InfoAnesth">Infos. Anesth.</a></li>
	{{if $isPrescriptionInstalled && $dPconfig.dPcabinet.CPrescription.view_prescription}}
	  <li onmousedown="Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null,'', null, false);">
	    <a href="#prescription_sejour">Trait. et prescription</a>
	  </li>
  {{/if}}
	{{if $dPconfig.dPcabinet.CConsultAnesth.show_facteurs_risque}}
    <li onmousedown="refreshFacteursRisque();"><a href="#facteursRisque">Facteurs de risque</a></li>
  {{/if}}
  <li><a href="#fdrConsult">Docs. et Réglements</a></li>
</ul>
<hr class="control_tabs" />


<!-- Tabs -->
<div id="AntTrait" style="display: none;">{{include file="../../dPcabinet/templates/inc_ant_consult.tpl" sejour_id=$consult->_ref_consult_anesth->_ref_sejour->_id}}</div>

<div id="Constantes" style="display: none;">
  <!-- We put a fake form for the ExamCompFrm form, before we insert the real one -->
  <form name="edit-constantes-medicales" action="?" method="post" onsubmit="return false">
    <input type="hidden" name="_last_poids" value="{{$consult->_ref_patient->_ref_constantes_medicales->poids}}" />
    <input type="hidden" name="_last__vst" value="{{$consult->_ref_patient->_ref_constantes_medicales->_vst}}" />
  </form>
</div>

<div id="Exams" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/acc_examens_clinique.tpl"}}</div>
<div id="Intub" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/intubation.tpl"}}</div>

{{if $app->user_prefs.ccam_consultation == 1}}
<div id="Actes" style="display: none;">
  <ul id="tab-actes" class="control_tabs">
    <li><a href="#ccam">Actes CCAM</a></li>
    <li><a href="#ngap">Actes NGAP</a></li>
    {{if $consult->sejour_id}}
    <li><a href="#cim">Diagnostics</a></li>
    {{/if}}
  </ul>
  <hr class="control_tabs"/>
  
  <div id="ccam" style="display: none;">
    {{assign var="module" value="dPcabinet"}}
    {{assign var="subject" value=$consult}}
    {{mb_include module=dPsalleOp template=inc_codage_ccam}}
  </div>
  
  <div id="ngap" style="display: none;">
    <div id="listActesNGAP">
      {{assign var="_object_class" value="CConsultation"}}
	    {{mb_include module=dPcabinet template=inc_codage_ngap}}
    </div>
  </div>
  
  {{if $consult->sejour_id}}
  <div id="cim" style="display: none;">
    {{assign var="sejour" value=$consult->_ref_sejour}}
    {{include file="../../dPsalleOp/templates/inc_diagnostic_principal.tpl" modeDAS="1"}}
  </div>
  {{/if}}
</div>
{{/if}}

<div id="ExamsComp" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/acc_examens_complementaire.tpl"}}</div>
<div id="InfoAnesth" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/acc_infos_anesth.tpl"}}</div>

{{if $isPrescriptionInstalled && $dPconfig.dPcabinet.CPrescription.view_prescription}}
<div id="prescription_sejour" style="display: none"></div>
{{/if}}

{{if $dPconfig.dPcabinet.CConsultAnesth.show_facteurs_risque}}
<div id="facteursRisque" style="display: none;"></div>
{{/if}}
	
<div id="fdrConsult" style="display: none;">{{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}</div>
