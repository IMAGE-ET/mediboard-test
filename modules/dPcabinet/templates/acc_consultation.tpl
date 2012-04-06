{{assign var="chir_id" value=$consult->_ref_plageconsult->chir_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{mb_include module=salleOp template=js_codage_ccam}}
{{assign var=sejour_id value=$consult->sejour_id}}

{{assign var="rpu" value=""}}
{{assign var="mutation_id" value=""}}
{{if $consult->sejour_id && $consult->_ref_sejour->_ref_rpu->_id}}
  {{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
  {{assign var="mutation_id" value=$rpu->mutation_sejour_id}}
{{/if}}

{{mb_script module="dPmedicament" script="equivalent_selector"}}

<script type="text/javascript">

function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    if (!Object.isUndefined(show_obs)) {
      urlSuivi.addParam("_show_obs", show_obs);
    }
    if (!Object.isUndefined(show_trans)) {
      urlSuivi.addParam("_show_trans", show_trans);
    }
    if (!Object.isUndefined(show_const)) {
      urlSuivi.addParam("_show_const", show_const);
    }
    urlSuivi.requestUpdate("suivisoins", {onComplete: function() { Control.Modal.close(); } });
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
    //url.addParam("selection[]", ["poids", "ta", "temperature", "pouls"]);
	  url.requestUpdate("Constantes");
		constantesMedicalesDrawn = true;
	}
};

function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'', null, false);
}

function loadResultLabo(sejour_id) {
  var url = new Url("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('Imeds');
}

Main.add(function () {
  var tabsConsult = Control.Tabs.create('tab-consult', false);
  {{if ($app->user_prefs.ccam_consultation == 1)}}
	  {{if !($consult->sejour_id && $mutation_id)}}
	    var tabsActes = Control.Tabs.create('tab-actes', false);
	  {{/if}}
  {{/if}}
	  
  {{if $consult->sejour_id && $rpu && !$mutation_id}}
  loadSuivi({{$rpu->sejour_id}});
  {{/if}}

  {{if @$modules.dPImeds->mod_active && $consult->sejour_id}}
    if($('Imeds')){
      loadResultLabo('{{$consult->sejour_id}}');
    }
  {{/if}}
});
</script>

<ul id="tab-consult" class="control_tabs">
  {{if $rpu}}
	  <li><a href="#rpuConsult">
	     RPU 
	    {{mb_include module=planningOp template=inc_vw_numdos nda=$consult->_ref_sejour->_NDA}}
	    </a>
	  </li>
  {{/if}}
  
  <li><a href="#AntTrait">Antécédents</a></li>
 
  {{if "dPprescription"|module_active && $consult->sejour_id && $modules.dPprescription->_can->read && !$conf.dPprescription.CPrescription.prescription_suivi_soins}}
  <li {{if !$mutation_id}}onmousedown="Prescription.reloadPrescSejour('', '{{$consult->sejour_id}}','', '', null, null, null,'', null, false);"{{/if}}>
    <a href="#prescription_sejour">
      Prescription Séjour
    </a>
  </li>
  {{/if}}
 
  {{if $rpu}}
  <li><a href="#suivisoins">Suivi soins</a></li>
  {{/if}}
  <li onmousedown="refreshConstantesMedicales();"><a href="#Constantes">Constantes</a></li>
  <li><a href="#Examens">Examens</a></li>
  
  {{if @$modules.dPImeds->mod_active && $consult->sejour_id}}
    <li><a href="#Imeds">Labo</a></li>
  {{/if}}
  
  {{if $app->user_prefs.ccam_consultation == 1}}
  <li><a href="#Actes">Cotation</a></li>
  {{/if}}
  
  {{if $consult->_is_dentiste}}
    <li><a href="#etat_dentaire">Etat dentaire</a></li>
    <li><a href="#devenir_dentaire">Projet thérapeutique</a></li>
  {{/if}}
  
  <li><a href="#fdrConsult">Documents</a></li>
  <li><a href="#reglement">Réglements</a></li>
</ul>
<hr class="control_tabs" />

{{if $consult->sejour_id}}
  {{if $rpu}}
    <div id="rpuConsult" style="display: none;">
		  {{mb_include module=urgences template=inc_vw_rpu}}
		</div>
	{{/if}}

<div id="prescription_sejour" style="display: none;">
  {{if $mutation_id}}
	  <div class="small-info">
	    Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
	  </div>
  {{/if}}
</div>
{{/if}}

<div id="AntTrait" style="display: none;">{{mb_include module=cabinet template=inc_ant_consult}}</div>

{{if $rpu}}
<div id="suivisoins" style="display:none">
  {{if $mutation_id}}
	  <div class="small-info">
	    Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
	  </div>
  {{/if}}
</div>
{{/if}}

<div id="Constantes" style="display: none"></div>

<div id="Examens" style="display: none;">
  {{mb_include module=cabinet template=inc_main_consultform}}
</div>

{{if @$modules.dPImeds->mod_active && $consult->sejour_id}}
<div id="Imeds" style="display: none;">
  <div class="small-info">
    Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
    consulter les résultats de laboratoire disponibles pour le patient concerné.
  </div>
</div>
{{/if}}
    
{{if $app->user_prefs.ccam_consultation == 1 }}
<div id="Actes" style="display: none;">
  {{if $mutation_id}}
	  <div class="small-info">
	    Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
	  </div>
  {{else}}
    {{assign var="sejour" value=$consult->_ref_sejour}}
	  <ul id="tab-actes" class="control_tabs">
	    {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
  	    <li><a href="#ccam">Actes CCAM</a></li>
  	    <li><a href="#ngap">Actes NGAP</a></li>
      {{/if}}
	    {{if $sejour && $sejour->_id}}
	    <li><a href="#cim">Diagnostics</a></li>	    
	    {{/if}}
	    {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation}}
	    <li><a href="#fraisdivers">Frais divers</a></li>
	    {{/if}}
      {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
      <li><a href="#tarmed_tab">Tarmed</a></li>
      {{/if}}
    </ul>
    <hr class="control_tabs"/>
	  
	  <div id="ccam" style="display: none;">
	    {{assign var="module" value="dPcabinet"}}
	    {{assign var="subject" value=$consult}}
	    {{mb_include module=salleOp template=inc_codage_ccam}}
	  </div>
	  
	  <div id="ngap" style="display: none;">
	    <div id="listActesNGAP">
	      {{assign var="_object_class" value="CConsultation"}}
		    {{mb_include module=cabinet template=inc_codage_ngap}}
	    </div>
	  </div>
	  
	  {{if $sejour && $sejour->_id}}
	  <div id="cim" style="display: none;">
	    {{assign var=sejour value=$consult->_ref_sejour}}
	    {{mb_include module=salleOp template=inc_diagnostic_principal modeDAS="1"}}
	  </div>
	  {{/if}}
	  
	  {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation}}     
    <div id="fraisdivers" style="display: none;">
      {{mb_include module=ccam template=inc_frais_divers object=$consult}}
    </div>
    {{/if}}
    
    {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
    <div id="tarmed_tab" >
      <div id="listActesTarmed">
        {{mb_include module=tarmed template=inc_codage_tarmed }}
      </div>
    </div>
    {{/if}}
	{{/if}}
</div>
{{/if}}

{{if $consult->_is_dentiste}}
  <div id="etat_dentaire">
    {{mb_include module=cabinet template="inc_consult_anesth/intubation"}}
  </div>
  <div id="devenir_dentaire">
    {{mb_include module=cabinet template="inc_devenir_dentaire"}}
  </div>
{{/if}}

<div id="fdrConsult" style="display: none;">
  {{mb_include module=cabinet template=inc_fdr_consult}}
</div>

<!-- Reglement -->
{{mb_script module="dPcabinet" script="reglement"}}
<script type="text/javascript">
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register('{{$consult->_id}}');
</script>