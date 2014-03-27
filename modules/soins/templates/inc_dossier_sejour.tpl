{{if $popup}}
  {{mb_script module="patients"    script="patient"         ajax=true}}
  {{mb_script module="soins"       script="plan_soins"      ajax=true}}
  {{mb_script module="planningOp"  script="cim10_selector"  ajax=true}}
  {{mb_script module="compteRendu" script="document"        ajax=true}}
  {{mb_script module="compteRendu" script="modele_selector" ajax=true}}
  {{mb_script module="cabinet"     script="file"            ajax=true}}

  {{if "dPmedicament"|module_active}}
    {{mb_script module="medicament" script="medicament_selector" ajax=true}}
    {{mb_script module="medicament" script="equivalent_selector" ajax=true}}
  {{/if}}

  {{if "dPprescription"|module_active}}
    {{mb_script module="prescription" script="element_selector" ajax=true}}
    {{mb_script module="prescription" script="prescription"     ajax=true}}
  {{/if}}
{{/if}}

{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher" ajax=true}}
{{/if}}

{{assign var=prescription_id value=$sejour->_ref_prescription_sejour->_id}}

<script>
  loadResultLabo = function(sejour_id) {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('Imeds');
  }

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
    var url = new Url("patients", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", '{{$sejour->_guid}}');
    url.addParam("paginate", 1);
    url.requestUpdate("constantes-medicales");
  }

  loadDocuments = function() {
    var url = new Url("hospi", "httpreq_documents_sejour");
    url.addParam("sejour_id" , '{{$sejour->_id}}');
    url.requestUpdate("docs");
  }

  loadAntecedents = function() {
    var url = new Url("cabinet","httpreq_vw_antecedents");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.addParam("show_header", 1);
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

  refreshConstantesMedicales = function(context_guid, paginate, count) {
    if(context_guid) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      url.addParam("paginate", paginate || 0);
      if (count) {
        url.addParam("count", count);
      }
      url.requestUpdate("constantes-medicales");
    }
  }

  if (!window.loadSuivi) {
    loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
      if (!sejour_id) {
        return;
      }
      
      updateNbTrans(sejour_id);
      
      var url = new Url("hospi", "httpreq_vw_dossier_suivi");
      url.addParam("sejour_id", sejour_id);
      url.addParam("user_id", user_id);
      if (!Object.isUndefined(cible)) {
        url.addParam("cible", cible);
      }
      if (!Object.isUndefined(show_obs)) {
        url.addParam("_show_obs", show_obs);
      }
      if (!Object.isUndefined(show_trans)) {
        url.addParam("_show_trans", show_trans);
      }
      if (!Object.isUndefined(show_const)) {
        url.addParam("_show_const", show_const);
      }
      url.requestUpdate("dossier_suivi");
    }
  }
  
  // Cette fonction est dupliquée
  updateNbTrans = function (sejour_id) {
    var url = new Url("hospi", "ajax_count_transmissions");
    url.addParam("sejour_id", sejour_id);
    url.requestJSON(function(count)  {
      Control.Tabs.setTabCount('dossier_suivi', count);
    });
  }
  
  printDossierSoins = function(){
    var url = new Url;
    url.setModuleAction("soins", "print_dossier_soins");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.popup("850", "500", "Dossier complet");
  }
  
  Main.add(function() {
    Prescription.mode_pharma = "{{$mode_pharma}}";

    tab_sejour = Control.Tabs.create('tab-sejour');
    tab_sejour.setActiveTab('{{$default_tab}}');
    tab_sejour.activeLink.onmousedown();
   
		window.DMI_operation_id = "{{$operation_id}}";
  });
</script>

<ul id="tab-sejour" class="control_tabs">
  <li><a href="#suivi_clinique" onmousedown="loadSuiviClinique();">{{tr}}CSejour.suivi_clinique{{/tr}}</a></li>
  <li onmousedown="loadConstantes();"><a href="#constantes-medicales">{{tr}}CPatient.surveillance{{/tr}}</a></li>
  <li><a href="#dossier_traitement" onmousedown="loadSuiviSoins();">{{tr}}CSejour.suivi_soins{{/tr}}</a></li>
  {{if $isPrescriptionInstalled}}
    <li><a href="#prescription_sejour" onmousedown="loadPrescription();">Prescription</a></li>
  {{/if}}
  {{if $isImedsInstalled}}
    <li><a href="#Imeds" onmousedown="loadResultLabo('{{$sejour->_id}}');">Labo</a></li>
  {{/if}}
  <li><a href="#docs" onmousedown="loadDocuments();">{{tr}}CMbObject-back-documents{{/tr}}</a></li>
  <li><a href="#antecedents" onmousedown="loadAntecedents();">{{tr}}IDossierMedical-back-antecedents{{/tr}}</a></li>
  <li style="float: right">
    <button type="button" class="button print" onclick="printDossierSoins();">Dossier soins</button>
    {{if !$popup}}
      <button type="button" class="cancel" onclick="closeModal();">{{tr}}Close{{/tr}}</button>
    {{/if}}
  </li>
</ul>

<div id="suivi_clinique" style="display: none;"></div>
<div id="constantes-medicales" style="display: none;"></div>
<div id="dossier_traitement" style="display: none;"></div>
{{if $isPrescriptionInstalled}}
  <div id="prescription_sejour" style="text-align: left; display: none;"></div>
{{/if}}
{{if $isImedsInstalled}}
  <div id="Imeds" style="display: none;"></div>
{{/if}}
<div id="docs" style="display: none;"></div>
<div id="antecedents" style="display: none;"></div>