{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var="chir_id"        value=$consult->_ref_plageconsult->chir_id}}
{{assign var="module"         value="dPcabinet"}}
{{assign var="sejour_id"      value=$consult->sejour_id}}
{{assign var="rpu"            value=""}}
{{assign var="mutation_id"    value=""}}
{{assign var="object"         value=$consult}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{assign var="sejour"         value=$consult->_ref_sejour}}

{{if $consult->sejour_id && $sejour && $sejour->_ref_rpu && $sejour->_ref_rpu->_id}}
  {{assign var="rpu" value=$sejour->_ref_rpu}}
  {{assign var="mutation_id" value=$rpu->mutation_sejour_id}}
  {{if $mutation_id == $consult->sejour_id}}
    {{assign var="mutation_id" value=""}}
  {{/if}}
{{/if}}

{{mb_include module=salleOp template=js_codage_ccam}}
{{mb_script module=medicament script=equivalent_selector}}
{{mb_script module=soins script=plan_soins}}
{{mb_script module=planningOp script=cim10_selector}}
<script>
  function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
    if (!sejour_id) {
      return;
    }
    var urlSuivi = new Url("hospi", "httpreq_vw_dossier_suivi");
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
    if (!Object.isUndefined(show_header)) {
      urlSuivi.addParam("show_header", show_header);
    }
    urlSuivi.requestUpdate("dossier_suivi");
  }

  function submitSuivi(oForm) {
    var sejour_id = oForm.sejour_id.value;
    onSubmitFormAjax(oForm, loadSuivi.curry(sejour_id));
  }

  var constantesMedicalesDrawn = false;
  function refreshConstantesMedicales (force) {
    if (!constantesMedicalesDrawn || force) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("patient_id", {{$consult->_ref_patient->_id}});
      url.addParam("context_guid", "{{$consult->_guid}}");
      url.addParam("infos_patient", 1);
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes-medicales");
      constantesMedicalesDrawn = true;
    }
  }

  function reloadPrescription(prescription_id){
    Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'', null, false);
  }

  function loadResultLabo() {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", '{{$consult->sejour_id}}');
    url.requestUpdate('Imeds');
  }

  function loadAntTrait() {
    var url = new Url("cabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id", "{{$consult->sejour_id}}");
    url.addParam("patient_id", "{{$consult->patient_id}}");
    url.addParam("show_header", 0);
    url.requestUpdate("AntTrait");
  }

  function loadActes() {
    var url = new Url("cabinet", "ajax_vw_actes");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("Actes");
  }

  function loadRPU() {
    var url = new Url("urgences", "ajax_vw_rpu");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("rpuConsult");
  }

  function loadDocs() {
    var url = new Url("cabinet", "ajax_vw_documents");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("fdrConsult");
  }

  function loadExams() {
    var url = new Url("cabinet", "ajax_vw_examens");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("Examens");
  }

  function loadSuiviLite() {
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
  }

  function loadSuiviSoins() {
    PlanSoins.loadTraitement('{{$consult->sejour_id}}',null,'','administration');
    loadSuiviLite();
  }

  Main.add(function() {
    tabsConsult = Control.Tabs.create('tab-consult', false);
    {{if $rpu}}
      loadRPU();
    {{elseif $app->user_prefs.dPcabinet_displayFirstTab == "Examens"}}
    tabsConsult.setActiveTab("Examens");
    loadExams();
    {{else}}
    loadAntTrait();
    {{/if}}
    if (tabsConsult.activeLink.key == "reglement") {
      Reglement.reload(true);
    }
  });
</script>

<ul id="tab-consult" class="control_tabs">
  {{if $rpu}}
    <li>
      <a href="#rpuConsult">
        {{tr}}soins.tab.rpu{{/tr}}
        {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
      </a>
    </li>
  {{/if}}

  <li onmousedown="this.onmousedown = ''; loadAntTrait()">
    <a id="acc_consultation_a_Atcd" href="#AntTrait" {{if $tabs_count.AntTrait == 0}}class="empty"{{/if}}>
      {{tr}}soins.tab.antecedent_and_treatment{{/tr}} <small>({{$tabs_count.AntTrait}})</small>
    </a>
  </li>

  <li onmousedown="refreshConstantesMedicales();">
    <a href="#constantes-medicales" {{if $tabs_count.Constantes == 0}}class="empty"{{/if}}>
      {{tr}}soins.tab.surveillance{{/tr}} <small>({{$tabs_count.Constantes}})</small>
    </a>
  </li>

  {{if "dPprescription"|module_active && $consult->sejour_id && $modules.dPprescription->_can->read && !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
    <li {{if !$mutation_id}}onmousedown="loadSuiviSoins();"{{/if}}>
      <a href="#dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}" {{if $tabs_count.dossier_traitement == 0}}class="empty"{{/if}}>
        {{tr}}soins.tab.suivi_soins{{/tr}} <small>({{$tabs_count.dossier_traitement}})</small>
      </a>
    </li>
  {{elseif $rpu}}
    <li {{if !$mutation_id}}onmousedown="loadSuivi('{{$rpu->sejour_id}}')"{{/if}}>
      <a href="#dossier_suivi" {{if $tabs_count.dossier_suivi == 0}}class="empty"{{/if}}>
        {{tr}}soins.tab.suivi_soins{{/tr}} <small>({{$tabs_count.dossier_suivi}})</small>
      </a>
    </li>
  {{/if}}

  {{if "dPprescription"|module_active && $consult->sejour_id && $modules.dPprescription->_can->read && !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
    <li {{if !$mutation_id}}onmousedown="Prescription.reloadPrescSejour('', '{{$consult->sejour_id}}','', '', null, null, null,'', null, false);"{{/if}}>
      <a href="#prescription_sejour" {{if $tabs_count.prescription_sejour == 0}}class="empty"{{/if}}>
        {{tr}}soins.tab.prescription{{/tr}} <small>({{$tabs_count.prescription_sejour}})</small>
      </a>
    </li>
  {{/if}}


  <li onmousedown="this.onmousedown = ''; loadExams()">
    <a href="#Examens" {{if $tabs_count.Examens == 0}}class="empty"{{/if}}>
      {{tr}}soins.tab.examens{{/tr}} <small>({{$tabs_count.Examens}})</small>
    </a>
  </li>

  {{if $app->user_prefs.ccam_consultation == 1}}
    <li onmousedown="this.onmousedown = ''; loadActes()">
      <a id="acc_consultation_a_Actes" href="#Actes" {{if $tabs_count.Actes == 0}}class="empty"{{/if}}>
        {{tr}}soins.tab.actes{{/tr}} <small>({{$tabs_count.Actes}})</small>
      </a>
    </li>
  {{/if}}

  {{if "dPImeds"|module_active && $consult->sejour_id}}
    <li onmousedown="this.onmousedown = ''; loadResultLabo();">
      <a href="#Imeds">{{tr}}soins.tab.labo{{/tr}}</a>
    </li>
  {{/if}}

  {{if $consult->_is_dentiste}}
    <li>
      <a href="#etat_dentaire">{{tr}}soins.tab.etat_dentaire{{/tr}}</a>
    </li>
    <li>
      <a href="#devenir_dentaire">{{tr}}soins.tab.projet_therapeutique{{/tr}}</a>
    </li>
  {{/if}}

  <li onmousedown="this.onmousedown = ''; loadDocs()">
    <a href="#fdrConsult" {{if $tabs_count.fdrConsult == 0}}class="empty"{{/if}}>
      {{tr}}soins.tab.documents{{/tr}} <small>({{$tabs_count.fdrConsult}})</small>
    </a>
  </li>

  <li onmousedown="Reglement.reload(true);">
    <a id="a_reglements_consult" href="#reglement" {{if $tabs_count.reglement == 0}}class="empty"{{/if}}>
      {{tr}}soins.tab.reglements{{/tr}} <small>({{$tabs_count.reglement}})</small>
    </a>
  </li>
</ul>

{{if $consult->sejour_id}}
  <div id="rpuConsult" style="display: none;"></div>

  {{if "dPprescription"|module_active &&
       $consult->sejour_id            &&
       $modules.dPprescription->_can->read &&
       !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
    <div id="prescription_sejour" style="display: none;">
      {{if $mutation_id}}
        <div class="small-info">
          Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
        </div>
      {{/if}}
    </div>

    <div id="dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}" style="display: none;">
      {{if $mutation_id}}
        <div class="small-info">
          Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
        </div>
      {{elseif "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
        {{mb_include module=soins template=inc_dossier_soins_widgets}}
      {{/if}}
    </div>

  {{elseif $rpu}}
    <div id="dossier_suivi" style="display:none">
      {{if $mutation_id}}
        <div class="small-info">
          Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
        </div>
      {{/if}}
    </div>
  {{/if}}
{{/if}}

<div id="AntTrait" style="display: none;"></div>

<div id="constantes-medicales" style="display: none"></div>

<div id="Examens" style="display: none;"></div>

{{if "dPImeds"|module_active && $consult->sejour_id}}
  <div id="Imeds" style="display: none;">
    <div class="small-info">
      Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
      consulter les résultats de laboratoire disponibles pour le patient concerné.
    </div>
  </div>
{{/if}}

{{if $app->user_prefs.ccam_consultation == 1}}
  <div id="Actes" style="display: none;">
    {{if $mutation_id}}
      <div class="small-info">
        Ce patient a été hospitalisé, veuillez vous référer au dossier de soin de son séjour.
      </div>
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

<div id="fdrConsult" style="display: none;"></div>

<!-- Reglement -->
{{mb_script module="cabinet" script="reglement"}}
<script>
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register(false);
</script>
