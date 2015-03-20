
{{mb_script module="patients"    script="patient"         ajax=true}}
{{mb_script module="soins"       script="plan_soins"      ajax=true}}
{{mb_script module="planningOp"  script="cim10_selector"  ajax=true}}
{{mb_script module="compteRendu" script="document"        ajax=true}}
{{mb_script module="compteRendu" script="modele_selector" ajax=true}}
{{mb_script module="files"       script="file"            ajax=true}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector" ajax=true}}
  {{mb_script module="medicament" script="equivalent_selector" ajax=true}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="element_selector" ajax=true}}
  {{mb_script module="prescription" script="prescription"     ajax=true}}
{{/if}}

{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher" ajax=true}}
{{/if}}
{{assign var="do_subject_aed" value="do_sejour_aed"}}
{{assign var="module" value="dPhospi"}}
{{assign var=object value=$sejour}}
{{mb_include module=salleOp template=js_codage_ccam}}
{{assign var=prescription_id value=$sejour->_ref_prescription_sejour->_id}}

<style>
  div.shadow {
    box-shadow: 0 8px 5px -3px rgba(0, 0, 0, .4);
  }
</style>

<script>
  loadResultLabo = function(sejour_id) {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('Imeds');
  };

  loadSuiviClinique = function() {
    var url = new Url("soins", "ajax_vw_suivi_clinique");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestUpdate("suivi_clinique");
  };

  loadSuiviSoins = function() {
    PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}','','administration', null, null, null, null, null, 1);
  };

  loadPrescription = function() {
    $('prescription_sejour').update('');
    Prescription.hide_header = true;
    Prescription.reloadPrescSejour('{{$prescription_id}}','{{$sejour->_id}}', null, null, null, null, null, null, null, null, null, '');
  };

  loadLabo = function() {
    loadResultLabo('{{$sejour->_id}}');
  };
  
  loadConstantes = function() {
    var url = new Url("patients", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", '{{$sejour->_guid}}');
    url.addParam("paginate", 1);
    if (window.oGraphs) {
      url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
    }
    url.requestUpdate("constantes-medicales");
  };

  loadDocuments = function() {
    var url = new Url("hospi", "httpreq_documents_sejour");
    url.addParam("sejour_id" , '{{$sejour->_id}}');
    url.addParam("with_patient", 1);
    url.requestUpdate("docs");
  };

  loadAntecedents = function() {
    var url = new Url("cabinet","httpreq_vw_antecedents");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.addParam("show_header", 0);
    url.requestUpdate('antecedents')
  };

  loadGrossesse = function() {
    {{if $sejour->_ref_grossesse}}
      var url = new Url('maternite', 'ajax_edit_grossesse', "action");
      url.addParam('grossesse_id', '{{$sejour->_ref_grossesse->_id}}');
      url.addParam('parturiente_id', '{{$patient->_id}}');
      url.addParam('with_buttons', 1);
      url.addParam('standalone', 1);
      url.requestUpdate("grossesse");
    {{/if}}
  };

  loadActes = function(sejour_id, praticien_id) {
    if($('listActesNGAP')){
      loadActesNGAP(sejour_id);
    }
    if($('ccam')){
      {{if $conf.dPccam.CCodeCCAM.use_new_association_rules}}
        loadCodagesCCAM(sejour_id);
      {{else}}
        ActesCCAM.refreshList(sejour_id, praticien_id);
      {{/if}}
    }
    if($('cim')){
      reloadDiagnostic(sejour_id, '1');
    }
    if ($('tarif')) {
      loadTarifsSejour(sejour_id);
    }
    if ($('tarmed')) {
      ActesTarmed.refreshListSejour(sejour_id, praticien_id);
    }
    if ($('caisse')) {
      ActesCaisse.refreshListSejour(sejour_id, praticien_id);
    }
  };

{{if $conf.dPccam.CCodeCCAM.use_new_association_rules}}
  loadCodagesCCAM = function(sejour_id, date, from, to) {
    var url = new Url('soins', 'ajax_codages_ccam_sejour');
    url.addParam('sejour_id', sejour_id);
    if (date) url.addParam('date', date);
    if (from) url.addParam('from', from);
    if (to) url.addParam('to', to);
    url.requestUpdate('ccam');
  }
{{/if}}

  loadActesNGAP = function (sejour_id){
    var url = new Url("dPcabinet", "httpreq_vw_actes_ngap");
    url.addParam("object_id", sejour_id);
    url.addParam("object_class", "CSejour");
    url.requestUpdate('listActesNGAP');
  };

  loadTarifsSejour = function (sejour_id) {
    var url = new Url("soins", "ajax_tarifs_sejour");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("tarif");
  };

  reloadDiagnostic = function (sejour_id, modeDAS) {
    var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modeDAS", modeDAS);
    url.requestUpdate("cim");
  };

  closeModal = function() {
    modalWindow.close();
    if (window.refreshLinePancarte){
      refreshLinePancarte('{{$prescription_id}}');
    }
    if(window.refreshLineSejour){ 
      refreshLineSejour('{{$sejour->_id}}'); 
    }
  };

  refreshConstantesMedicales = function(context_guid, paginate, count) {
    if(context_guid) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      url.addParam("paginate", paginate || 0);
      if (count) {
        url.addParam("count", count);
      }
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes-medicales");
    }
  };

  if (!window.loadSuivi) {
    loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
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
      if (!Object.isUndefined(show_header)) {
        url.addParam("show_header", show_header);
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
  };
  
  printDossierSoins = function(){
    var url = new Url;
    url.setModuleAction("soins", "print_dossier_soins");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.popup("850", "500", "Dossier complet");
  };

  printPlanSoins = function() {
    var url = new Url("soins", "offline_plan_soins");
    url.addParam("sejours_ids", "{{$sejour->_id}}");
    url.addParam("mode_dupa", 1);
    url.pop(1000, 600);
  };

  reloadAtcd = function() {
    var url = new Url('soins', 'httpreq_vw_antecedent_allergie');
    url.addParam('sejour_id', "{{$sejour->_id}}");
    url.requestUpdate('atcd_allergies');
  };

  toggleListSejour = function() {
    $('left-column').toggle();
    ViewPort.SetAvlSize('content-dossier-soins', 1.0);
  };

  loadSuiviLite = function() {
    // Transmissions
    PlanSoins.loadLiteSuivi('{{$sejour->_id}}');

    // Constantes
    var url = new Url("patients", "httpreq_vw_constantes_medicales_widget");
    url.addParam("context_guid", "{{$sejour->_guid}}");
    url.requestUpdate("constantes-medicales-widget");

    // Formulaires
    {{if "forms"|module_active}}
      {{unique_id var=unique_id_widget_forms}}
      ExObject.loadExObjects("{{$sejour->_class}}", "{{$sejour->_id}}", "{{$unique_id_widget_forms}}", 0.5);
    {{/if}}
  };

  Main.add(function() {
    Prescription.mode_pharma = "{{$mode_pharma}}";
    File.use_mozaic = 1;
    window.currentSejourId = '{{$sejour->_id}}';

    tab_sejour = Control.Tabs.create('tab-sejour', false, {
      afterChange: function(container) {
        switch (container.id) {
          case 'suivi_clinique':
            loadSuiviClinique();
            break;
          case 'constantes-medicales':
            loadConstantes();
            break;
          case 'dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}':
            loadSuiviSoins();
            {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
              loadSuiviLite();
            {{/if}}
            break;
          case 'prescription_sejour':
            loadPrescription();
            break;
          case 'Actes':
            loadActes({{$sejour->_id}}, {{$sejour->_ref_praticien->_id}});
            break;
          case 'Imeds':
            loadResultLabo('{{$sejour->_id}}');
            break;
          case 'docs':
            loadDocuments();
            break;
          case 'antecedents':
            loadAntecedents();
            break;

          case 'grossesse':
            loadGrossesse();
            break;
        }
      }
    });
    tab_sejour.setActiveTab('{{$default_tab}}');

    {{if $app->user_prefs.ccam_sejour == 1 }}
      var tab_actes = Control.Tabs.create('tab-actes', false);
    {{/if}}

		window.DMI_operation_id = "{{$operation_id}}";
    ViewPort.SetAvlSize('content-dossier-soins', 1.0);
    var content = $("content-dossier-soins");
    var header = $("header-dossier-soins");
    content.on('scroll', function() {
      header.setClassName('shadow', content.scrollTop);
    });
  });
</script>

<div id="header-dossier-soins" style="position: relative;">
  <div id="patient_banner">
    {{mb_include module=soins template=inc_patient_banner}}
  </div>

  <ul id="tab-sejour" class="control_tabs">
    {{if !$modal && !$popup}}
      <li>
        <button type="button" class="hslip notext compact" style="vertical-align: bottom; float: left;" onclick="toggleListSejour();" title="Afficher/cacher la colonne de gauche"></button>
      </li>
    {{/if}}
    <li><a href="#suivi_clinique">{{tr}}soins.tab.synthese{{/tr}}</a></li>
    <li><a href="#constantes-medicales">{{tr}}soins.tab.surveillance{{/tr}}</a></li>
    <li><a href="#dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}">{{tr}}soins.tab.suivi_soins{{/tr}}</a></li>

    {{if $isPrescriptionInstalled}}
      <li><a href="#prescription_sejour">{{tr}}soins.tab.prescription{{/tr}}</a></li>
    {{/if}}

    {{if $app->user_prefs.ccam_sejour == 1 }}
      <li onmousedown="loadActes({{$sejour->_id}}, {{$sejour->_ref_praticien->_id}});"><a href="#Actes">{{tr}}soins.tab.cotation-infirmiere{{/tr}}</a></li>
    {{/if}}

    {{if $isImedsInstalled}}
      <li><a href="#Imeds">{{tr}}soins.tab.labo{{/tr}}</a></li>
    {{/if}}

    <li>
      <a href="#docs">{{tr}}soins.tab.documents{{/tr}}</a>
    </li>
    <li>
      <a href="#antecedents">{{tr}}soins.tab.antecedent_and_treatment{{/tr}}</a>
    </li>
    {{if $sejour->_ref_grossesse && $sejour->_ref_grossesse->_id}}
      <li>
        <a href="#grossesse">{{tr}}soins.tab.grossesse{{/tr}}</a>
      </li>
    {{/if}}
    <li style="float: right">
      {{if "telemis"|module_active}}
        {{mb_include module=telemis template=inc_viewer_link patient=$sejour->_ref_patient label="Imagerie" button=true class="imagerie"}}
      {{/if}}
      {{if "soins dossier_soins show_bouton_plan_soins"|conf:"CGroups-$g"}}
        <button type="button" class="print" onclick="printPlanSoins()">Plan de soins</button>
      {{/if}}
      <button type="button" class="print" onclick="printDossierSoins();">Dossier soins</button>
      {{if !$popup && $modal}}
        <button type="button" class="cancel" onclick="closeModal();">{{tr}}Close{{/tr}}</button>
      {{/if}}
    </li>
  </ul>
</div>

<div id="content-dossier-soins" style="width: 100%;">
  <div id="suivi_clinique" style="display: none;"></div>
  <div id="constantes-medicales" style="display: none;"></div>
  <div id="dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}" style="display: none;">
    {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
      {{mb_include module=soins template=inc_dossier_soins_widgets}}
    {{/if}}
  </div>
  {{if $isPrescriptionInstalled}}
    <div id="prescription_sejour" style="text-align: left; display: none;"></div>
  {{/if}}
  {{if $app->user_prefs.ccam_sejour == 1}}
    <div id="Actes" style="display: none;">
      <table class="form">
        <tr>
          <td style="">
            <ul id="tab-actes" class="control_tabs">
              {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
                <li id="tarif" style="float: right;"></li>
                <li><a href="#one">Actes CCAM</a></li>
                <li><a href="#two">Actes NGAP</a></li>
                {{if $conf.dPccam.CCodable.use_frais_divers.CSejour}}
                  <li><a href="#fraisdivers">Frais divers</a></li>
                {{/if}}
                <li><a href="#three">Diagnostics</a></li>
              {{/if}}
              {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
                <li><a href="#tarmed_tab">TARMED</a></li>
                <li><a href="#caisse_tab">{{tr}}CPrestationCaisse{{/tr}}</a></li>
              {{/if}}
            </ul>
            <hr class="control_tabs" />

            <table class="form">
              {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
                <tr id="one" style="display: none;">
                  <td id="ccam">
                  </td>
                </tr>
                <tr id="two" style="display: none;">
                  <td id="listActesNGAP">
                  </td>
                </tr>
                {{if $conf.dPccam.CCodable.use_frais_divers.CSejour}}
                  <tr id="fraisdivers" style="display: none;">
                    <td>
                      {{mb_include module=ccam template=inc_frais_divers}}
                    </td>
                  </tr>
                {{/if}}
                <tr id="three" style="display: none;">
                  <td id="cim">
                  </td>
                </tr>
              {{/if}}
              {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
                {{mb_script module=tarmed script=actes ajax=true}}
                <tr id="tarmed_tab" style="display: none;">
                  <td id="tarmed">
                    <div id="listActesTarmed">
                    </div>
                  </td>
                </tr>
                <tr id="caisse_tab" style="display: none;">
                  <td id="caisse">
                    <div id="listActesCaisse">
                    </div>
                  </td>
                </tr>
              {{/if}}
            </table>
          </td>
        </tr>
      </table>
    </div>
  {{/if}}
  {{if $isImedsInstalled}}
    <div id="Imeds" style="display: none;"></div>
  {{/if}}
  <div id="docs" style="display: none;"></div>
  <div id="antecedents" style="display: none;"></div>
  <div id="grossesse" style="display: none"></div>
</div>