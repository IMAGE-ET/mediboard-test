{{assign var="chir_id" value=$consult->_ref_plageconsult->_ref_chir->_id}}
{{assign var="object" value=$consult}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{assign var="module" value="cabinet"}}
{{mb_include module=salleOp template=js_codage_ccam}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="element_selector"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

{{mb_script module="dPcabinet" script="reglement"}}

<script type="text/javascript">

{{if $isPrescriptionInstalled && $conf.dPcabinet.CPrescription.view_prescription}}
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
  url.addParam("dossier_anesth_id", "{{$consult->_ref_consult_anesth->_id}}");
  url.requestUpdate("facteursRisque");
};

Main.add(function () {
  tabsConsultAnesth = Control.Tabs.create('tab-consult-anesth', false, {
     afterChange: function(newContainer) {
      switch (newContainer.id) {
        case "prescription_sejour" :
          Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null,'', null, false);       
          break;
      }    
     }
  });
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
  <li><a href="#ExamsComp">Exam. Comp.</a></li>
  <li><a href="#InfoAnesth">Infos. Anesth.</a></li>
	{{if $isPrescriptionInstalled && $conf.dPcabinet.CPrescription.view_prescription}}
	  <li>
	    <a href="#prescription_sejour">Prescription</a>
	  </li>
  {{/if}}
	{{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
    <li onmousedown="refreshFacteursRisque();"><a href="#facteursRisque">Facteurs de risque</a></li>
  {{/if}}
  {{if $app->user_prefs.ccam_consultation == 1}}
  <li><a href="#Actes">Actes</a></li>
  {{/if}}
  <li><a href="#fdrConsult">Documents</a></li>
  <li><a href="#reglement">Réglements</a></li>
</ul>

<hr class="control_tabs" />

<!-- Tabs -->
<div id="AntTrait" style="display: none;">
  {{mb_include module=cabinet template=inc_ant_consult sejour_id=$consult->_ref_consult_anesth->_ref_sejour->_id}}</div>

<div id="Constantes" style="display: none;">
  <!-- We put a fake form for the ExamCompFrm form, before we insert the real one -->
  <form name="edit-constantes-medicales" action="?" method="post" onsubmit="return false">
    <input type="hidden" name="_last_poids" value="{{$consult->_ref_patient->_ref_constantes_medicales->poids}}" />
    <input type="hidden" name="_last__vst" value="{{$consult->_ref_patient->_ref_constantes_medicales->_vst}}" />
  </form>
</div>

<div id="Exams" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/acc_examens_clinique}}
</div>
<div id="Intub" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/intubation}}
</div>
<div id="ExamsComp" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/acc_examens_complementaire}}
</div>
<div id="InfoAnesth" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/acc_infos_anesth}}
</div>

{{if $isPrescriptionInstalled && $conf.dPcabinet.CPrescription.view_prescription}}
<div id="prescription_sejour" style="display: none"></div>
{{/if}}

{{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
<div id="facteursRisque" style="display: none;"></div>
{{/if}}

{{if $app->user_prefs.ccam_consultation == 1}}
<div id="Actes" style="display: none;">
  <ul id="tab-actes" class="control_tabs">
    {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
      <li><a href="#ccam">Actes CCAM</a></li>
      <li><a href="#ngap">Actes NGAP</a></li>
      {{if $consult->sejour_id}}
        <li><a href="#cim">Diagnostics</a></li>
      {{/if}}
    {{/if}}
    {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
      <li><a href="#tarmed_tab">Tarmed</a></li>
      <li><a href="#caisse_tab">Caisses</a></li>
    {{/if}}
  </ul>
  <hr class="control_tabs"/>
  
  <div id="ccam" style="display: none;">
    {{assign var="subject" value=$consult}}
    {{mb_include module=salleOp template=inc_codage_ccam}}
  </div>
  
  <div id="ngap" style="display: none;">
    <div id="listActesNGAP">
      {{assign var="_object_class" value="CConsultation"}}
      {{mb_include module=cabinet template=inc_codage_ngap}}
    </div>
  </div>
  
  {{if $consult->sejour_id}}
    <div id="cim" style="display: none;">
      {{assign var="sejour" value=$consult->_ref_sejour}}
      {{mb_include module=salleOp template=inc_diagnostic_principal modeDAS="1"}}
    </div>
  {{/if}}
  
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
    <div id="tarmed_tab" >
      <div id="listActesTarmed">
        {{mb_include module=tarmed template=inc_codage_tarmed }}
      </div>
    </div>
    <div id="caisse_tab" style="display:none">
      <div id="listActesCaisse">
        {{mb_include module=tarmed template=inc_codage_caisse }}
      </div>
    </div>
  {{/if}}
</div>
{{/if}}
	
<div id="fdrConsult" style="display: none;">
  {{mb_include module=cabinet template=inc_fdr_consult}}
</div>

<!-- Reglement -->
<script type="text/javascript">
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register('{{$consult->_id}}');
</script>
