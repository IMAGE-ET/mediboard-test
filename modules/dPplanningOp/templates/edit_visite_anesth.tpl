{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="prescription_med"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">

printFicheAnesth = function(consult_id) {
  var url = new Url("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consult_id);
  url.popup(700, 500, "printFiche");
}

submitAnesth = function(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
  	onComplete: function() { 
  		reloadAnesth(oForm.operation_id.value) 
  	}
  });
}

reloadPrescription = function(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
}

signVisiteAnesth = function(anesth_id) {
  alert('anesth numéro ' + anesth_id);
}

reloadAnesth = function(operation_id){
  var url = new Url("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", { 
  	onComplete: function() { 
  		ActesCCAM.refreshList(operation_id,"{{$operation->chir_id}}"); 
  	}
  } );	
}

var constantesMedicalesDrawn = false;
refreshConstantesHack = function(sejour_id) {
  (function(){
    if (constantesMedicalesDrawn == false && $('constantes-medicales').visible() && sejour_id) {
      refreshConstantesMedicales('CSejour-'+sejour_id);
      constantesMedicalesDrawn = true;
    }
  }).delay(0.5);
}

refreshConstantesMedicales = function(context_guid) {
  if(context_guid) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    url.requestUpdate("constantes-medicales");
  }
}
Main.add(function () {
  // Initialisation des onglets
	if ($('main_tab_group')){
    Control.Tabs.create('main_tab_group', true);
	}
  
  if($('antecedents')){
    var url = new Url("dPcabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id","{{$operation->sejour_id}}");
    url.requestUpdate("antecedents");
  }

  if($('constantes-medicales')){
    constantesMedicalesDrawn = false;
    refreshConstantesHack('{{$operation->sejour_id}}');
  }

	{{if $isPrescriptionInstalled}}
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('','{{$operation->_ref_sejour->_id}}', null, null, '{{$operation->_id}}', null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
  }
  {{/if}}

  if($('Imeds_tab')){
    var url = new Url("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", {{$operation->_ref_sejour->_id}});
    url.requestUpdate('Imeds_tab');
  }
});

</script>

{{assign var="selOp" value=$operation}}
{{assign var="sejour" value=$operation->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}
{{if $selOp->prat_visite_anesth_id}}
  {{assign var="modeles_prat_id" value=$selOp->prat_visite_anesth_id}}
{{elseif $selOp->_ref_consult_anesth->_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_consult_anesth->_ref_consultation->_ref_chir->_id}}
{{/if}}

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#anesth_tab">Anesth.</a></li>
  <li><a href="#antecedents">Atcd.</a></li>
  <li onmousedown="refreshConstantesHack('{{$operation->sejour_id}}');"><a href="#constantes-medicales">Constantes</a></li>
  {{if $isPrescriptionInstalled && $dPconfig.dPcabinet.CPrescription.view_prescription}}
    <li><a href="#prescription_sejour_tab">Prescription</a></li>
	{{/if}}
  {{if $isImedsInstalled}}
    <li><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
</ul>
  
<hr class="control_tabs" />

<!-- Anesthesie -->
<div id="anesth_tab" style="display:none;">
  <div id="info_anesth">
  {{include file="../../dPsalleOp/templates/inc_vw_info_anesth.tpl"}}
  </div>
</div>

<!-- Antécédents -->
<div id="antecedents" style="display:none;"></div>

<!-- Constantes -->
<div id="constantes-medicales" style="display: none;"></div>

<!-- Prescription -->
{{if $isPrescriptionInstalled}}
  <div id="prescription_sejour_tab" style="display:none;">
    <div id="prescription_sejour"></div>
  </div>
{{/if}}

<!-- Résultats labo -->
{{if $isImedsInstalled}}
  <div id="Imeds_tab" style="display:none"></div>
{{/if}}