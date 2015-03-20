{{if $require_check_list}}
  <table class="main layout">
    <tr>
      {{foreach from=$daily_check_lists item=check_list}}
        <td>
          <h2>{{$check_list->_ref_list_type->title}}</h2>
          {{if $check_list->_ref_list_type->description}}
            <p>{{$check_list->_ref_list_type->description}}</p>
          {{/if}}

          {{mb_include module=salleOp template=inc_edit_check_list
          check_list=$check_list
          check_item_categories=$check_list->_ref_list_type->_ref_categories
          personnel=$listValidateurs
          list_chirs=$listChirs
          list_anesths=$listAnesths
          }}
        </td>
      {{/foreach}}
    </tr>
  </table>
  {{mb_return}}
{{/if}}

{{if !$selOp->_id}}
  <div class="big-info">
    Veuillez sélectionner une intervention dans la liste pour pouvoir :
    <ul>
      <li>sélectionner le personnel en salle</li>
      <li>effectuer l'horodatage</li>
      <li>coder les diagnostics</li>
      <li>coder les actes</li>
      <li>consulter le dossier</li>
    </ul>
  </div>
  {{mb_return}}
{{/if}}

{{assign var="sejour"  value=$selOp->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}
{{assign var="module"  value="dPsalleOp"}}
{{assign var="object"  value=$selOp}}
{{assign var="do_subject_aed" value="do_planning_aed"}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector" ajax=1}}
  {{mb_script module="medicament" script="equivalent_selector" ajax=1}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="element_selector" ajax=1}}
  {{mb_script module="prescription" script="prescription"     ajax=1}}
{{/if}}

{{mb_script module="compteRendu"  script="document"        ajax=1}}
{{mb_script module="compteRendu"  script="modele_selector" ajax=1}}
{{mb_script module="files"        script="file"            ajax=1}}
{{mb_script module="bloodSalvage" script="bloodSalvage"    ajax=1}}
{{mb_script module="soins"        script="plan_soins"      ajax=1}}
{{mb_script module="planningOp"   script="cim10_selector"  ajax=1}}

{{mb_include module=salleOp template=js_codage_ccam}}

{{if $conf.dPsalleOp.enable_surveillance_perop}}
  {{mb_script module=patients script=supervision_graph ajax=1}}
{{/if}}

<script>
  printFicheAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", "print_fiche");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.popup(700, 500, "printFiche");
  };

  submitTiming = function(oForm) {
    onSubmitFormAjax(oForm, function() {
      reloadTiming($V(oForm.operation_id));
    });
  };

  reloadTiming = function(operation_id) {
    {{if $object->_id}}
    var url = new Url("salleOp", "httpreq_vw_timing");
    url.addParam("operation_id", operation_id);
    url.requestUpdate("timing", ActesCCAM.refreshList.curry({{$object->_id}},{{$object->_praticien_id}}));
    {{/if}}
  };

  reloadTimingTab = function() {
    reloadTiming('{{$selOp->_id}}');
    reloadPersonnel('{{$selOp->_id}}');
  };

  submitAnesth = function(oForm) {
    onSubmitFormAjax(oForm, function() {
      if (Prescription.updatePerop) {
        Prescription.updatePerop('{{$selOp->sejour_id}}');
      }
      reloadAnesth($V(oForm.operation_id));
      var formVisite = getForm("visiteAnesth");
      if (formVisite && $V(formVisite.date_visite_anesth) == 'current') {
        $V(formVisite.prat_visite_anesth_id, $V(oForm.anesth_id));
      }
    });
  };

  reloadAnesth = function(operation_id) {
    var url = new Url("salleOp", "httpreq_vw_anesth");
    url.addParam("operation_id", operation_id);
    url.requestUpdate("anesth", function() {
      if (window.reloadDocumentsAnesth) {
        reloadDocumentsAnesth();
      }
      ActesCCAM.refreshList(operation_id,"{{$selOp->chir_id}}");
    });
  };

  reloadDiagnostic = function(sejour_id, modeDAS) {
    var url = new Url("salleOp", "httpreq_diagnostic_principal");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modeDAS", modeDAS);
    url.requestUpdate("cim");
  };

  reloadPersonnel = function(operation_id) {
    var url = new Url("salleOp", "httpreq_vw_personnel");
    url.addParam("operation_id", operation_id);
    url.requestUpdate("listPersonnel");
  };

  reloadAntecedents = function() {
    var url = new Url("cabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id","{{$selOp->sejour_id}}");
    url.requestUpdate("antecedents");
  };

  reloadBloodSalvage = function() {
    var url = new Url("bloodSalvage", "httpreq_vw_bloodSalvage");
    url.addParam("op","{{$selOp->_id}}");
    url.requestUpdate("bloodsalvage_form");
  };

  reloadImeds = function() {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.requestUpdate('Imeds_tab');
  };

  reloadActes = function() {
    var url = new Url("salleOp", "ajax_refresh_actes");
    url.addParam("operation_id", "{{$selOp->_id}}");
    url.requestUpdate("codage_actes");
  };

  var constantesMedicalesDrawn = false;
  refreshConstantesHack = function(sejour_id) {
    (function() {
      if (constantesMedicalesDrawn == false && $('constantes-medicales').visible() && sejour_id) {
        refreshConstantesMedicales('CSejour-'+sejour_id, "{{if $selOp->_ref_salle && $selOp->_ref_salle->_ref_bloc && $selOp->_ref_salle->_ref_bloc->_guid}}{{$selOp->_ref_salle->_ref_bloc->_guid}}{{else}}all{{/if}}");
        constantesMedicalesDrawn = true;
      }
    }).delay(0.5);
  };

  refreshConstantesMedicales = function(context_guid, host_guid) {
    if (context_guid) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      if (host_guid) {
        url.addParam("host_guid", host_guid);
      }
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes-medicales");
    }
  };

  reloadPrescription = function(prescription_id) {
    Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null);
  };

  reloadSurveillancePerop = function() {
    if($('surveillance_perop')){
      var url = new Url("salleOp", "ajax_vw_surveillance_perop");
      url.addParam("operation_id","{{$selOp->_id}}");
      url.requestUpdate("surveillance_perop");
    }
  };

  confirmeCloture = function() {
    return confirm("Action irréversible. Seul le service PSMI pourra modifier le codage de vos actes. Confirmez-vous la cloture de votre cotation pour aujourd'hui ?");
  };

  loadSuiviSoins = function() {
    PlanSoins.loadTraitement('{{$selOp->sejour_id}}', '{{$date}}', '', 'administration');
    {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
    loadSuiviLite();
    {{/if}}
  };

  Main.add(function() {
    // Sauvegarde de l'operation_id selectionné (utile pour l'ajout de DMI dans la prescription)
    window.DMI_operation_id = "{{$selOp->_id}}";

    // Initialisation des onglets
    var tabs = Control.Tabs.create('main_tab_group', true);
    var tabName = tabs.activeContainer.id;

    switch (tabName) {
      case "disp_vasculaire":
        loadPosesDispVasc();
        reloadBloodSalvage();
        break;
      case "diag_tab":
        reloadDiagnostic('{{$selOp->sejour_id}}', '{{$modeDAS}}');
        break;
      case "codage_tab":
        reloadActes();
        break;
      case "dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}":
        loadSuiviSoins();
        break;
      case "prescription_sejour_tab":
        Prescription.reloadPrescSejour('', '{{$selOp->_ref_sejour->_id}}', null, null, '{{$selOp->_id}}', null, null);
        break;
      case "constantes-medicales":
        constantesMedicalesDrawn = false;
        refreshConstantesHack('{{$selOp->sejour_id}}');
        break;
      case "antecedents":
        reloadAntecedents();
        break;
      case "Imeds_tab":
        reloadImeds();
        break;
      case "grossesse":
        refreshGrossesse('{{$selOp->_id}}');
        break;
      case "surveillance_perop":
        reloadSurveillancePerop();
        break;
      case "timing_tab":
      default:
        // Par défault, le volet timing est le premier chargé
        reloadTimingTab();
    }

    // Effet sur le programme
    if ($('listplages') && $('listplages-trigger')) {
      new PairEffect("listplages", {sEffect: "appear", bStartVisible: true });
    }
  });

  printFicheBloc = function(interv_id) {
    var url = new Url("dPsalleOp", "print_feuille_bloc");
    url.addParam("operation_id", interv_id);
    url.popup(700, 700, 'FeuilleBloc');
  };

  loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
    if (sejour_id) {
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
  };

  submitSuivi = function(oForm) {
    sejour_id = $V(oForm.sejour_id);
    onSubmitFormAjax(oForm, function() {
      loadSuivi(sejour_id);
      if ($V(oForm.object_class) != "" || $V(oForm.libelle_ATC) != "") {
        // Refresh de la partie administration
        PlanSoins.loadTraitement(sejour_id, "{{$date}}", "", "administration");
      }
    });
  };

  createObservationResultSet = function(object_guid, pack_id) {
    if($('surveillance_perop')){
      var url = new Url("patients", "ajax_edit_observation_result_set");
      url.addParam("object_guid", object_guid);
      url.addParam("pack_id", pack_id);
      url.requestModal(600, 600, {
        onClose: reloadSurveillancePerop
      });
    }
  };

  editObservationResultSet = function(result_set_id, pack_id, result_id) {
    if($('surveillance_perop')){
      var url = new Url("patients", "ajax_edit_observation_result_set");
      url.addParam("result_set_id", result_set_id);
      url.addParam("pack_id", pack_id);
      url.addParam("result_id", result_id);
      url.requestModal(600, 600, {
        onClose: reloadSurveillancePerop
      });
    }
  };

  loadPosesDispVasc = function() {
    var url = new Url("planningOp", "ajax_list_pose_disp_vasc");
    url.addParam("operation_id", "{{$selOp->_id}}");
    url.addParam("sejour_id",    "{{$selOp->sejour_id}}");
    url.addParam("operateur_ids", "{{$operateurs_disp_vasc}}");
    url.requestUpdate("list-pose-dispositif-vasculaire");
  };

  refreshGrossesse = function(operation_id) {
    var url = new Url("maternite", "ajax_vw_grossesse");
    url.addParam('operation_id', operation_id);
    url.requestUpdate('grossesse');
  };

  infoAnapath = function(field) {
    if ($V(field) == 1) {
      var url = new Url("salleOp", "ajax_info_anapath");
      url.addParam("operation_id", $V(field.form.operation_id));
      url.requestModal();
    }
    onSubmitFormAjax(field.form);
  };

  infoBacterio = function(field) {
    if ($V(field) == 1) {
      var url = new Url("salleOp", "ajax_info_bacterio");
      url.addParam("operation_id", $V(field.form.operation_id));
      url.requestModal();
    }
    onSubmitFormAjax(field.form);
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
</script>

<!-- Informations générales sur l'intervention et le patient -->
{{mb_include module="salleOp" template="inc_header_operation"}}

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  {{if !$conf.dPsalleOp.mode_anesth && (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit)}}
    <li onmousedown="reloadTimingTab()"><a href="#timing_tab">Timings</a></li>
  {{/if}}

  <li onmousedown="loadPosesDispVasc(); reloadBloodSalvage();"><a href="#disp_vasculaire">Dispositifs vasc.</a></li>

  {{if !$conf.dPsalleOp.mode_anesth}}
    {{if (!$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && $codage_prat))}}
      <li onmousedown="reloadActes()"><a href="#codage_tab">{{tr}}CCodable-actes{{/tr}}</a></li>
      <li onmousedown="reloadDiagnostic('{{$selOp->sejour_id}}', '{{$modeDAS}}')"><a href="#diag_tab">Diags.</a></li>
    {{/if}}

    {{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && $currUser->_is_anesth)}}
      {{assign var=callback value=refreshVisite}}
      <li onmouseup="reloadAnesth('{{$selOp->_id}}'); {{if "dPprescription"|module_active}}Prescription.updatePerop('{{$selOp->sejour_id}}');{{/if}}"><a href="#anesth_tab">Anesth.</a></li>
    {{/if}}
    {{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
      <li><a href="#dossier_tab">Chir.</a></li>
    {{/if}}

    {{if "dPprescription"|module_active}}
      <li onmouseup="loadSuiviSoins();">
        <a href="#dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}">Suivi soins</a>
      </li>
      <li onmousedown="Prescription.reloadPrescSejour('', '{{$selOp->_ref_sejour->_id}}', null, null, '{{$selOp->_id}}', null, null);">
        <a href="#prescription_sejour_tab">Prescription</a>
      </li>
    {{/if}}

    <li onmousedown="refreshConstantesHack('{{$selOp->sejour_id}}');"><a href="#constantes-medicales">Surveillance</a></li>
    <li onmousedown="reloadAntecedents()"><a href="#antecedents">Atcd.</a></li>
  {{/if}}

  {{if $isImedsInstalled}}
    <li onmousedown="reloadImeds()"><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
  <li style="float: right">
    {{if "vivalto"|module_active && $can->edit}}
      {{mb_include module=vivalto template=inc_button_dmi operation=$selOp}}
    {{/if}}
    <button type="button" class="print" onclick="printFicheBloc('{{$selOp->_id}}');">Feuille de bloc</button>
  </li>

  {{if "maternite"|module_active && $sejour->grossesse_id}}
    <li onmouseup="refreshGrossesse('{{$selOp->_id}}')">
      <a href="#grossesse">Accouchement</a>
    </li>
  {{elseif $conf.dPsalleOp.enable_surveillance_perop}}
    <li onmouseup="reloadSurveillancePerop();"><a href="#surveillance_perop">Perop</a></li>
  {{/if}}
</ul>

<!-- Timings + Personnel -->
{{if !$conf.dPsalleOp.mode_anesth && (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit)}}
  <div id="timing_tab" style="display:none">
    <div id="check_lists">
      {{mb_include module=salleOp template=inc_vw_operation_check_lists}}
    </div>
    <div id="timing"></div>
    <div id="listPersonnel"></div>
  </div>
{{/if}}

<div id="disp_vasculaire" style="display:none">
  <fieldset style="clear: both;">
    <legend>{{tr}}CPoseDispositifVasculaire{{/tr}}</legend>
    <div id="list-pose-dispositif-vasculaire"></div>
  </fieldset>

  {{if "bloodSalvage"|module_active && (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit)}}
    <fieldset>
      <legend>{{tr}}CCellSaver{{/tr}}</legend>
      <div id="bloodsalvage_form"></div>
    </fieldset>
  {{/if}}
</div>

{{if !$conf.dPsalleOp.mode_anesth}}
  {{if (!$currUser->_is_praticien || $currUser->_is_praticien && $can->edit || $currUser->_is_praticien && $codage_prat)}}
    <!-- codage des acte ccam et ngap -->
    <div id="codage_tab" style="display: none">
      <form name="infoFactu" action="?" method="post">
        <input type="hidden" name="m" value="planningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$selOp}}
        <input type="hidden" name="del" value="0" />
        <table class="form">
          <tr>
            <th style="text-align: right">
              {{mb_label object=$selOp field=anapath onclick="infoAnapath($(this.getAttribute('for')+'_1'));"}}
            </th>
            <td>
              {{mb_field object=$selOp field=anapath typeEnum="radio" onChange="infoAnapath(this);"}}
            </td>
            <th style="text-align: right">
              {{mb_label object=$selOp field=prothese}}
            </th>
            <td>
              {{mb_field object=$selOp field=prothese typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
            </td>
          </tr>
          <tr>
            <th style="text-align: right">
              {{mb_label object=$selOp field=labo onclick="infoBacterio($(this.getAttribute('for')+'_1'));"}}
            </th>
            <td style="vertical-align:middle;">
              {{mb_field object=$selOp field=labo typeEnum="radio" onChange="infoBacterio(this);"}}
            </td>
            <td colspan="2"></td>
          </tr>
        </table>
      </form>

      <div id="codage_actes"></div>
    </div>

    <!-- codage diagnostics CIM -->
    <div id="diag_tab" style="display:none">
      <div id="cim"></div>
    </div>
  {{/if}}

  <!-- Anesthesie -->
  {{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && $currUser->_is_anesth)}}
    <div id="anesth_tab" style="display:none">
      {{mb_include module=salleOp template=inc_vw_info_anesth}}
    </div>
  {{/if}}

  {{if !$currUser->_is_praticien || ($currUser->_is_praticien && $can->edit) || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
    <!-- Documents et facteurs de risque -->
    {{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
    <div id="dossier_tab" style="display:none">
      <table class="form">
        <tr>
          <th class="title">Documents</th>
        </tr>
        <tr>
          <td>
            <div id="documents">
              {{mb_include module=planningOp template=inc_documents_operation operation=$selOp}}
            </div>
            <div id="files">
              {{mb_include module=planningOp template=inc_files_operation operation=$selOp}}
            </div>
          </td>
        </tr>
      </table>
      <hr />
      <table class="tbl">
        <tr>
          <th class="title">Facteurs de risque</th>
        </tr>
      </table>
      {{mb_include module=cabinet template=inc_consult_anesth/inc_vw_facteurs_risque sejour=$selOp->_ref_sejour patient=$selOp->_ref_sejour->_ref_patient}}
    </div>
  {{/if}}


  <div id="constantes-medicales" style="display: none;"></div>
  <div id="antecedents" style="display:none"></div>

  {{if "dPprescription"|module_active}}
    <!-- Affichage de la prescription -->
    <div id="prescription_sejour_tab" style="display:none">
      <div id="prescription_sejour"></div>
    </div>

    <!-- Affichage du dossier de soins avec les lignes "bloc" -->
    <div id="dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}" style="display:none">
      {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
        {{mb_include module=soins template=inc_dossier_soins_widgets}}
      {{/if}}
    </div>
  {{/if}}
{{/if}}

{{if $isImedsInstalled}}
  <!-- Affichage de la prescription -->
  <div id="Imeds_tab" style="display:none"></div>
{{/if}}

{{if "maternite"|module_active && $sejour->grossesse_id}}
  <div id="grossesse" style="display: none;"></div>
{{elseif $conf.dPsalleOp.enable_surveillance_perop}}
  <div id="surveillance_perop" style="display:none"></div>
{{/if}}