{{mb_script module="dPcabinet" script="exam_dialog"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="files" script="file"}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription"}}
  {{mb_script module="dPprescription" script="prescription_editor"}}
  {{mb_script module="dPprescription" script="element_selector"}}
{{/if}}

{{assign var="object" value=$consult}}
{{assign var="mutation_id" value=""}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}

{{mb_include module=salleOp template=js_codage_ccam}}

{{if $consult->sejour_id && $consult->_ref_sejour && $consult->_ref_sejour->_ref_rpu && $consult->_ref_sejour->_ref_rpu->_id}}
  {{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
  {{assign var="mutation_id" value=$rpu->mutation_sejour_id}}
  {{if $mutation_id == $consult->sejour_id}}
    {{assign var="mutation_id" value=""}}
  {{/if}}
{{/if}}

<script>
  {{if !$consult->_canEdit}}
    App.readonly = true;
  {{/if}}
  
  function submitForm(oForm) {
    onSubmitFormAjax(oForm);
  }
  
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs_consult');
    {{if $consult_anesth->_id}}
      tabs.setActiveTab('exam_clinique');
    {{else}}
      tabs.setActiveTab('exams');
    {{/if}}

    {{if ($app->user_prefs.ccam_consultation == 1)}}
      {{if !($consult->sejour_id && $mutation_id)}}
        var tabsActes = Control.Tabs.create('tab-actes', false);
        loadTarifsConsult('{{$consult->sejour_id}}', '{{$consult->_ref_chir->_id}}', '{{$consult->_id}}');
      {{/if}}
    {{/if}}
  });

  function loadTarifsConsult(sejour_id, chir_id, consult_id) {
    var url = new Url('soins', 'ajax_tarifs_sejour');
    url.addParam('consult_id', consult_id);
    url.addParam('sejour_id', sejour_id);
    url.addParam('chir_id'  , chir_id);
    url.requestUpdate('tarif');
  }

  refreshVisite = function(operation_id) {
    var url = new Url('salleOp', 'ajax_refresh_visite_pre_anesth');
    url.addParam('operation_id', operation_id);
    url.addParam('callback', 'refreshVisite');
    url.requestUpdate('visite_pre_anesth');
  };

  reloadDiagnostic = function(sejour_id, modeDAS) {
    var url = new Url('salleOp', 'httpreq_diagnostic_principal');
    url.addParam('sejour_id', sejour_id);
    url.addParam('modeDAS', modeDAS);
    url.requestUpdate('cim');
  };
</script>

<!-- Formulaire pour réactualiseér -->
<form name="editFrmFinish" method="get">
  {{mb_key object=$consult}}
</form>

<ul id="tabs_consult" class="control_tabs">
  <li><a href="#antecedents">{{tr}}soins.tab.antecedent_and_treatment{{/tr}}</a></li>
  {{if !$consult_anesth->_id}}
    <li><a href="#exams">{{tr}}soins.tab.examens{{/tr}}</a></li>
  {{else}}
    <li><a href="#exam_clinique">{{tr}}soins.tab.examens{{/tr}}</a> </li>
    <li><a href="#intubation">{{tr}}soins.tab.intubation{{/tr}}</a>        </li>
    <li><a href="#exam_comp">{{tr}}soins.tab.examens_comp{{/tr}}</a>        </li>
    <li><a href="#infos_anesth">{{tr}}soins.tab.infos_anesth{{/tr}}</a>  </li>
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
      <li><a href="#facteurs_risque">{{tr}}soins.tab.facteurs_risque{{/tr}}</a></li>
    {{/if}}
    {{if $consult_anesth->operation_id}}
      <li><a href="#visite_pre_anesth">{{tr}}soins.tab.visite_pre_anesth{{/tr}}</a></li>
    {{/if}}
  {{/if}}
  {{if $app->user_prefs.ccam_consultation == 1}}
    <li><a href="#Actes">{{tr}}soins.tab.actes{{/tr}}</a></li>
  {{/if}}
  <li><a href="#fdrConsult">{{tr}}soins.tab.documents{{/tr}}</a></li>
</ul>

<div id="antecedents" style="display: none">
  {{if $patient->_ref_dossier_medical && !$patient->_ref_dossier_medical->_canEdit}}
    {{mb_include module=dPpatients template=CDossierMedical_complete object=$patient->_ref_dossier_medical}}
  {{else}}
    {{mb_include module=cabinet template="inc_ant_consult"}}
  {{/if}}

</div>
{{if !$consult_anesth->_id}}
  <div id="exams" style="display: none;">
    {{mb_include module=cabinet template="inc_main_consultform"}}
  </div>
{{else}}
  <div id="exam_clinique" style="display: none;">
    {{mb_include module=cabinet template="inc_consult_anesth/acc_examens_clinique"}}
  </div>
  <div id="intubation" style="display: none;">
    {{mb_include module=cabinet template="inc_consult_anesth/intubation"}}
  </div>
  <div id="exam_comp" style="display: none;">
    {{mb_include module=cabinet template="inc_consult_anesth/acc_examens_complementaire"}}
  </div>
  <div id="infos_anesth" style="display: none;">
    {{mb_include module=cabinet template="inc_consult_anesth/acc_infos_anesth"}}
  </div>
  {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
    <div id="facteurs_risque" style="display: none;">
      {{mb_include module=cabinet template="inc_consult_anesth/inc_vw_facteurs_risque"}}
    </div>
  {{/if}}
  {{if $consult_anesth->operation_id}}
    {{assign var=selOp value=$consult_anesth->_ref_operation}}
    {{assign var=callback value=refreshVisite}}
    {{assign var=currUser value=$userSel}}
    <div id="visite_pre_anesth">
      {{mb_include module=salleOp template=inc_visite_pre_anesth}}
    </div>
  {{/if}}
{{/if}}
{{if $app->user_prefs.ccam_consultation == 1}}
  <span id="tarif" style="float: right;margin-bottom: -20px;"></span>
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
        {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
          <li><a href="#fraisdivers">Frais divers</a></li>
        {{/if}}
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
          <li><a href="#tarmed_tab">Tarmed</a></li>
          <li><a href="#caisse_tab">Caisses</a></li>
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
          {{mb_include module=salleOp template=inc_diagnostic_principal modeDAS="1"}}
        </div>
      {{/if}}

      {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
        <div id="fraisdivers" style="display: none;">
          {{mb_include module=ccam template=inc_frais_divers object=$consult}}
        </div>
      {{/if}}

      {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
        {{mb_script module=tarmed script=actes ajax=true}}
        <script>
          Main.add(function() {
            ActesTarmed.loadList('{{$consult->_id}}', '{{$consult->_class}}', '{{$consult->_ref_chir->_id}}');
            ActesCaisse.loadList('{{$consult->_id}}', '{{$consult->_class}}', '{{$consult->_ref_chir->_id}}');
          });
        </script>
        <div id="tarmed_tab" style="display:none">
          <div id="listActesTarmed"></div>
        </div>
        <div id="caisse_tab" style="display:none">
          <div id="listActesCaisse"></div>
        </div>
      {{/if}}
    {{/if}}
  </div>
{{/if}}

<div id="fdrConsult" style="display: none;">
  {{mb_include module=cabinet template=inc_fdr_consult}}
</div>
