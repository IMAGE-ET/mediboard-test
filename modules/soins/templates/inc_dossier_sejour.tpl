{{if $popup}}
{{mb_script module="dPpatients" script="patient"}}
{{mb_script module="dPprescription" script="plan_soins"}}
{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="dPprescription" script="element_selector"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPprescription" script="prescription"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}
{{/if}}

{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

{{assign var=prescription_id value=$sejour->_ref_prescription_sejour->_id}}

<script type="text/javascript">
  loadSuiviClinique = function() {
    var url = new Url("soins", "ajax_vw_suivi_clinique");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestUpdate("suivi_clinique");
  }

  loadSuiviSoins = function() {
    PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}','','administration', null, null, null, null, null, 1);
  }

  loadPrescription = function() {
    $('prescription_sejour').update('');
    Prescription.reloadPrescSejour('{{$prescription_id}}','{{$sejour->_id}}');
  }

  loadLabo = function() {
    loadResultLabo('{{$sejour->_id}}');
  }
  
  loadConstantes = function() {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", '{{$sejour->_guid}}');
    url.requestUpdate("constantes");
  }

  loadDocuments = function() {
    var url = new Url("dPhospi", "httpreq_documents_sejour");
    url.addParam("sejour_id" , '{{$sejour->_id}}');
    url.requestUpdate("docs");
  }

  loadAntecedents = function() {
    var url = new Url("dPcabinet","httpreq_vw_antecedents");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestUpdate('antecedents')
  }

  closeModal = function() {
    modalWindow.close();
    if (window.refreshLinePancarte){
      refreshLinePancarte('{{$prescription_id}}');
    }
		if(window.refreshLineSejour){ 
		  refreshLineSejour('{{$sejour->_id}}'); 
		}
  }
  
  Main.add(function() {
    tab_sejour = Control.Tabs.create('tab-sejour');
    tab_sejour.setActiveTab('{{$default_tab}}');
		
		{{if $default_tab == "dossier_traitement"}}
    loadSuiviSoins();
    {{/if}}
		
		{{if $default_tab == "prescription_sejour"}}
    loadPrescription();
    {{/if}}
    
    {{if $isImedsInstalled}}
      if($('Imeds')){
        loadResultLabo(sejour_id);
      }
    {{/if}}
  });
</script>

<ul id="tab-sejour" class="control_tabs">
  <li><a href="#suivi_clinique" onmousedown="loadSuiviClinique()">{{tr}}CSejour.suivi_clinique{{/tr}}</a></li>
  <li><a href="#dossier_traitement" onmousedown="loadSuiviSoins();">{{tr}}CSejour.suivi_soins{{/tr}}</a></li>
  {{if $isPrescriptionInstalled}}
    <li><a href="#prescription_sejour" onmousedown="loadPrescription();">Prescription</a></li>
  {{/if}}
  {{if $isImedsInstalled}}
    <li><a href="#Imeds">Labo</a></li>
  {{/if}}
  <li><a href="#constantes" onmousedown="loadConstantes();">{{tr}}CPatient.surveillance{{/tr}}</a></li>
  <li><a href="#docs" onmousedown="loadDocuments();">{{tr}}CMbObject-back-documents{{/tr}}</a></li>
  <li><a href="#antecedents" onmousedown="loadAntecedents();">{{tr}}IDossierMedical-back-antecedents{{/tr}}</a></li>
  <li style="float: right">
    {{if !$popup}}
	    <button type="button" class="cancel" onclick="closeModal();">{{tr}}Close{{/tr}}</button>
    {{/if}}
	</li>
</ul>

<hr class="control_tabs" />

<div id="suivi_clinique" style="display: none;"></div>
<div id="dossier_traitement" style="display: none;"></div>
{{if $isPrescriptionInstalled}}
  <div id="prescription_sejour" style="display: none;"></div>
{{/if}}
{{if $isImedsInstalled}}
  <div id="Imeds" style="display: none;"></div>
{{/if}}
<div id="constantes" style="display: none;"></div>
<div id="docs" style="display: none;"></div>
<div id="antecedents" style="display: none;"></div>