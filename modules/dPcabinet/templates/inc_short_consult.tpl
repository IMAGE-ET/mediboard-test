{{mb_script module="dPcabinet" script="exam_dialog"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}

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

<script type="text/javascript">
  {{if !$consult->_canEdit}}
    App.readonly = true;
  {{/if}}
  
  function submitForm(oForm) {
    onSubmitFormAjax(oForm);
  }
  
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs_consult');
    {{if $consult_anesth->_id}}
      tabs.setActiveTab("exam_clinique");
    {{else}}
      tabs.setActiveTab("exams");
    {{/if}}

    {{if ($app->user_prefs.ccam_consultation == 1)}}
      {{if !($consult->sejour_id && $mutation_id)}}
        var tabsActes = Control.Tabs.create('tab-actes', false);
      {{/if}}
    {{/if}}
  });
  
  refreshVisite = function(operation_id) {
    var url = new Url("dPsalleOp", "ajax_refresh_visite_pre_anesth");
    url.addParam("operation_id", operation_id);
    url.addParam("callback", "refreshVisite");
    url.requestUpdate("visite_pre_anesth");
  };

  reloadDiagnostic = function(sejour_id, modeDAS) {
    var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modeDAS", modeDAS);
    url.requestUpdate("cim");
  };
</script>

<!-- Formulaire pour réactualiseér -->
<form name="editFrmFinish" method="get">
  {{mb_key object=$consult}}
</form>

<ul id="tabs_consult" class="control_tabs">
  <li>
    <a href="#antecedents">Antécédents</a>
  </li>
  {{if !$consult_anesth->_id}}
    <li>
      <a href="#exams">Examens</a>
    </li>
  {{else}}
    <li>
      <a href="#exam_clinique">Exam. Clinique</a>
    </li>
    <li>
      <a href="#intubation">Intubation</a>
    </li>
    <li>
      <a href="#exam_comp">Exam. Comp.</a>
    </li>
    <li>
      <a href="#infos_anesth">Infos. Anesth.</a>
    </li>
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
      <li>
        <a href="#facteurs_risque">Facteurs de risques</a>
      </li>
    {{/if}}
    {{if $consult_anesth->operation_id}}
      <li>
        <a href="#visite_pre_anesth">Visite pré-anesth.</a>
      </li>  
    {{/if}}
  {{/if}}
  {{if $app->user_prefs.ccam_consultation == 1}}
    <li><a href="#Actes">Cotation</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="antecedents" style="display: none">
  {{mb_include module=cabinet template="inc_ant_consult"}}
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
          <div id="tarmed_tab" style="display:none">
            <div id="listActesTarmed">
              {{mb_include module=tarmed template=inc_codage_tarmed }}
            </div>
          </div>
          <div id="caisse_tab" style="display:none">
            <div id="listActesCaisse">
              {{mb_include module=tarmed template=inc_codage_caisse}}
            </div>
          </div>
        {{/if}}
      {{/if}}
    </div>
  {{/if}}
{{/if}}