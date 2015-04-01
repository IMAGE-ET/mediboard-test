{{mb_script module="soins" script="plan_soins"}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
	{{mb_script module="dPprescription" script="element_selector"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

<script type="text/javascript">

var constantesMedicalesDrawn = false;
refreshConstantesHack = function(sejour_id) {
  (function(){
    if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
      refreshConstantesMedicales('CSejour-'+sejour_id);
      constantesMedicalesDrawn = true;
    }
  }).delay(0.5);
}

function refreshConstantesMedicales(context_guid) {
  if(context_guid) {
    var url = new Url("patients", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    if (window.oGraphs) {
      url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
    }
    url.requestUpdate("constantes");
  }
}

function loadPatient(patient_id) {
  var url = new Url("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CPatient");
  url.addParam("object_id",patient_id);
  url.requestUpdate('viewPatient');
}

function loadSejour(sejour_id) {
  var url = new Url("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CSejour");
  url.addParam("object_id",sejour_id);
  url.requestUpdate('viewSejourHospi');
}

function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    if (!Object.isUndefined(show_obs) && show_obs != null) {
      urlSuivi.addParam("_show_obs", show_obs);
    }
    if (!Object.isUndefined(show_trans) && show_trans != null) {
      urlSuivi.addParam("_show_trans", show_trans);
    }
    if (!Object.isUndefined(show_const) && show_const != null) {
      urlSuivi.addParam("_show_const", show_const);
    }
		if (!Object.isUndefined(show_header)) {
			urlSuivi.addParam("show_header", show_header);
		}
    urlSuivi.requestUpdate("dossier_suivi");
  }
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
    loadSuivi(sejour_id); 
    if(oForm.object_class.value != "" || oForm.libelle_ATC.value != ''){
      // Refresh de la partie administration
      PlanSoins.loadTraitement(sejour_id,'{{$date}}','','administration');
    }  
  } });
}

{{if $isPrescriptionInstalled}}
function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null);
}
{{/if}}

Main.add(function () {
  if($('tabs_reveil')){
    headerPrescriptionTabs = Control.Tabs.create('tabs_reveil', false);
  }
  {{if $operation->_id}}
  loadPatient('{{$sejour->patient_id}}');
  loadSejour('{{$sejour->_id}}');
	if($('Imeds_tab')){
    var url = new Url;
    url.setModuleAction("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestUpdate('Imeds_tab');
  }
	
	// Sauvegarde de l'operation_id selectionné (utile pour l'ajout de DMI dans la prescription)
  window.DMI_operation_id = "{{$operation->_id}}";
	{{/if}}	 
});

</script>

{{if $operation->_id}}
	{{assign var=patient value=$sejour->_ref_patient}}
	<table class="tbl">
	  <tr>
	    <th class="title text">
	      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
	        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
	      </a>
	      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
	        <img src="images/icons/edit.png" alt="modifier" />
	       </a>
	      {{$patient->_view}}
	      ({{$patient->_age}}
	      {{if $patient->_annees != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
	      &mdash; Dr {{$operation->_ref_chir->_view}}
	      <br />
	      {{if $operation->libelle}}{{$operation->libelle}} &mdash;{{/if}}
	      {{mb_label object=$operation field=cote}} : {{mb_value object=$operation field=cote}}
	    </th>
	  </tr>
	</table>
	
	<ul id="tabs_reveil" class="control_tabs">
		<li><a href="#viewPatient">Patient</a></li>
	  <li><a href="#viewSejourHospi">Séjour</a></li>
		  <li onmousedown="refreshConstantesHack('{{$sejour->_id}}');"><a href="#constantes">Constantes</a></li>
		{{if $isPrescriptionInstalled}}
	    <li onmousedown="PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}','','administration');"><a href="#dossier_traitement">Soins</a></li>
		  <li onmousedown="Prescription.reloadPrescSejour('','{{$sejour->_id}}', null, null, null, null, null);"><a href="#prescription_sejour">Prescription</a></li>
	  {{/if}}  
		<li><a href="#dossier_tab">Documents</a></li>
		{{if $isImedsInstalled}}
	    <li><a href="#Imeds_tab">Labo</a></li>
	  {{/if}}
	</ul>
	<hr class="control_tabs" />
	
	<div id="viewPatient"></div>
	<div id="viewSejourHospi" style="display: none;"></div>
	<div id="constantes" style="display: none;"></div>
	{{if $isPrescriptionInstalled}}
		<div id="dossier_traitement" style="display: none;"></div>
		<div id="prescription_sejour" style="display: none;"></div>
	{{/if}}
	
	<!-- Documents-->
	<div id="dossier_tab" style="display:none">
	  <table class="form">
	    <tr>
	      <th class="title">Documents</th>
	    </tr>
	    <tr>
	      <td>
	        <div id="documents">
	          {{mb_script module="dPcompteRendu" script="document"}}
	          {{mb_script module="dPcompteRendu" script="modele_selector"}}
	          {{mb_include module=planningOp template=inc_documents_operation}}
	        </div>
	      </td>
	    </tr>
	  </table>
	</div>
	{{if $isImedsInstalled}}
		<div id="Imeds_tab" style="display:none"></div>
	{{/if}}

{{else}}
	<div class="small-info">
		Veuillez sélectionner un patient dans l'onglet <strong>Salle de réveil</strong> pour accéder à ces soins.
	</div>
{{/if}}