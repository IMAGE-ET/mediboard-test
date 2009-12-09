{{assign var="chir_id" value=$consult->_ref_plageconsult->chir_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{mb_include module=dPsalleOp template=js_codage_ccam}}
{{assign var=sejour_id value=""}}
{{if $consult->sejour_id}}
  {{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
  {{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

<script type="text/javascript">
function setField(oField, sValue) {
  oField.value = sValue;
}

function loadSuivi(sejour_id, user_id) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.requestUpdate("suivisoins");
  }
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadSuivi(sejour_id); } });
}

var constantesMedicalesDrawn = false;
function refreshConstantesMedicales (force) {
	if (!constantesMedicalesDrawn || force) {
	  var url = new Url();
	  url.setModuleAction("dPhospi", "httpreq_vw_constantes_medicales");
	  url.addParam("patient_id", {{$consult->_ref_patient->_id}});
	  url.addParam("context_guid", "{{$consult->_guid}}");
	  url.requestUpdate("Constantes");
		constantesMedicalesDrawn = true;
	}
};

function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null, true, !Preferences.mode_readonly,'', null, false);
}

Main.add(function () {
  var tabsConsult = Control.Tabs.create('tab-consult', false);
  {{if $app->user_prefs.ccam_consultation == 1}}
	  {{if !($consult->sejour_id && $rpu->mutation_sejour_id)}}
	    var tabsActes = Control.Tabs.create('tab-actes', false);
	  {{/if}}
  {{/if}}
  {{if $consult->sejour_id && !$rpu->mutation_sejour_id}}
  loadSuivi({{$rpu->sejour_id}});
  {{/if}}
});
</script>

<ul id="tab-consult" class="control_tabs">
  {{if $consult->sejour_id}}
  <li><a href="#rpuConsult">
     RPU 
    {{if $consult->_ref_sejour->_num_dossier}}
      [{{$consult->_ref_sejour->_num_dossier}}]
    {{/if}}</a>
  </li>
   <li {{if !$rpu->mutation_sejour_id}}onclick="Prescription.reloadPrescSejour('', '{{$consult->sejour_id}}','', '', null, null, null, true, !Preferences.mode_readonly,'', null, false);"{{/if}}>
    <a href="#prescription_sejour">
      Prescription
    </a>
  </li>
  {{/if}}
  
  <li><a href="#AntTrait">Antécédents</a></li>
  {{if $consult->sejour_id}}
  <li><a href="#suivisoins">Suivi soins</a></li>
  {{/if}}
  <li onmousedown="refreshConstantesMedicales();"><a href="#Constantes">Constantes</a></li>
  <li><a href="#Examens">Examens</a></li>
  
  {{if $app->user_prefs.ccam_consultation == 1}}
  <li><a href="#Actes">Actes</a></li>
  {{/if}}
  
  <li><a href="#fdrConsult">Docs et Règlements</a></li>
</ul>
<hr class="control_tabs" />

{{if $consult->sejour_id}}
<div id="rpuConsult" style="display: none;">{{include file="../../dPurgences/templates/inc_vw_rpu.tpl"}}</div>
<div id="prescription_sejour" style="display: none;">
  {{if $rpu->mutation_sejour_id}}
	  <div class="small-info">
	    Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
	  </div>
  {{/if}}
</div>
{{/if}}

<div id="AntTrait" style="display: none;">{{include file="../../dPcabinet/templates/inc_ant_consult.tpl"}}</div>
{{if $consult->sejour_id}}
<div id="suivisoins" style="display:none">
  {{if $rpu->mutation_sejour_id}}
	  <div class="small-info">
	    Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
	  </div>
  {{/if}}
</div>
{{/if}}

<div id="Constantes" style="display: none"></div>

<div id="Examens" style="display: none;">
  {{include file="../../dPcabinet/templates/inc_main_consultform.tpl"}}
</div>

{{if $app->user_prefs.ccam_consultation == 1}}
<div id="Actes" style="display: none;">
  {{if $consult->sejour_id && $rpu->mutation_sejour_id}}
	  <div class="small-info">
	    Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
	  </div>
  {{else}}
		  <ul id="tab-actes" class="control_tabs">
		    <li><a href="#ccam">Actes CCAM</a></li>
		    <li><a href="#ngap">Actes NGAP</a></li>
		    {{if $consult->sejour_id}}
		    <li><a href="#cim">Diagnostics</a></li>
		    {{/if}}
	    </ul>
	    <hr class="control_tabs"/>
	  {{/if}}
	  
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

<div id="fdrConsult" style="display: none;">{{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}</div>