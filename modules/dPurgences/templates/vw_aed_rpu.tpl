{{mb_script module=dPpatients script=patient ajax=true}}
{{mb_script module=files script=file}}
{{mb_include module=files template=yoplet_uploader object=$sejour}}
{{assign var=gerer_circonstance value=$conf.dPurgences.gerer_circonstance}}
{{assign var=consult value=$rpu->_ref_consult}}
{{mb_default var=show_buttons_urgence value=false}}
{{if "ecap"|module_active}}
  {{assign var=show_buttons_urgence value="ecap Display show_buttons_urgence"|conf:"CGroups-$g"}}
{{/if}}
{{mb_script module=dPurgences script=CCirconstance}}

{{if !$group->service_urgences_id}}
  <div class="small-warning">{{tr}}dPurgences-no-service_urgences_id{{/tr}}</div>
  {{mb_return}}
{{/if}}

{{mb_script module=patients script=pat_selector}}
{{mb_script module=urgences script=contraintes_rpu}}
{{mb_script module=compteRendu script=modele_selector}}
{{mb_script module=compteRendu script=document}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
  {{mb_script module="prescription" script="element_selector"}}
  {{mb_script module="soins" script="plan_soins"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

<style>
  div.shadow {
    box-shadow: 0 8px 5px -3px rgba(0, 0, 0, .4);
  }
</style>

{{if $conf.ref_pays == 2}}
  <script>
    Main.add(function () {
      var save_colonne = false;
      {{if $rpu->_id}}
        save_colonne = true;
      {{/if}}
      var tab_rpu = Control.Tabs.create('tab-rpu', save_colonne, {afterChange: function(elt) {
        if (elt.id == 'dossier_infirmier') {
          addScroll();
        }
      }});
      var hash = Url.parse().fragment;
      if (hash == "Imeds") {
        {{if $rpu->_id}}
          Control.Tabs.activateTab("dossier_infirmier");
        {{/if}}
      }
    });
  </script>
  <ul id="tab-rpu" class="control_tabs">
    <li><a href="#admission">Echelle de tri</a></li>
    <li><a href="#dossier_infirmier">Dossier infirmier</a></li>
  </ul>

  <div id="admission" style="display:none;">
    {{mb_include module=dPurgences template=vw_aed_rpu2}}
  </div>
  {{if $rpu->_id}}
    <div id="dossier_infirmier" style="display:none;">
  {{else}}
    <div id="dossier_infirmier" style="display:none;">
      <div class="big-info">Veuillez renseigner le dossier infirmier</div>
    </div>
    {{mb_return}}
  {{/if}}
{{/if}}

<script>
  ContraintesRPU.contraintesProvenance = {{$contrainteProvenance|@json}};

  function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
    if (!sejour_id) {
      return;
    }

    var urlSuivi = new Url("hospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    urlSuivi.addParam("show_header", 1);
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

  function submitSuivi(oForm) {
    var sejour_id = $V(oForm.sejour_id);
    submitFormAjax(oForm, 'systemMsg', { onComplete: function() { Control.Modal.close(); loadSuivi(sejour_id); } });
  }

  function refreshConstantesMedicales(context_guid) {
    if (context_guid) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes-medicales");
    }
  }

  var constantesMedicalesDrawn = false;
  function refreshConstantesHack(sejour_id) {
    (function(){
      if (constantesMedicalesDrawn == false && $('constantes-medicales').visible() && sejour_id) {
        refreshConstantesMedicales('CSejour-'+sejour_id);
        constantesMedicalesDrawn = true;
      }
    }).delay(0.5);
  }

  function showExamens(consult_id) {
    if (!consult_id) {
      return;
    }

    var url = new Url("urgences", "ajax_show_examens");
    url.addParam("consult_id", consult_id);
    url.requestUpdate("examens");
  }

  function loadDocItems(sejour_id, consult_id) {
    if (!sejour_id) {
      return;
    }

    var url = new Url("urgences", "ajax_show_doc_items");
    url.addParam("sejour_id" , sejour_id);
    url.addParam("consult_id", consult_id);
    url.requestUpdate("doc-items");
  }

  function loadActes(sejour_id) {
    if (!sejour_id) {
      return;
    }

    var url = new Url("urgences", "ajax_show_actes");
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("actes");
  }

  function cancelRPU() {
    var oForm = document.editRPU;
    var oElement = oForm._annule;

    if (oElement.value == "0") {
      if (confirm("Voulez-vous vraiment annuler le dossier ?")) {
        oElement.value = "1";
        oForm.submit();
        return;
      }
    }

    if (oElement.value == "1") {
      if (confirm("Voulez-vous vraiment rétablir le dossier ?")) {
        oElement.value = "0";
        oForm.submit();
        return;
      }
    }
  }

  {{if $isPrescriptionInstalled}}
    function reloadPrescription(prescription_id){
      Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'');
    }
  {{/if}}

  function changeModeEntree(mode_entree) {
    loadTransfert(mode_entree);
    loadServiceMutation(mode_entree);
  }

  function changePecTransport(transport) {
   var pec_transport = transport.form.elements.pec_transport;
    if (transport.value === "perso" && $V(pec_transport) === "") {
      $V(pec_transport, "aucun");
    }
  }

  function changeProvenanceWithEntree(entree) {
    {{if "dPurgences CRPU provenance_domicile_pec_non_org"|conf:"CGroups-$g"}}
    if (entree.value === "8") {
      $V(entree.form.elements._provenance, "5");
    }
    {{/if}}
  }

  function loadTransfert(mode_entree){
    $('etablissement_entree_transfert').setVisible(mode_entree == 7);
    {{if "dPplanningOp CSejour required_from_when_transfert"|conf:"CGroups-$g"}}
      var oform = getForm('editRPU');
      var provenance = $(oform._provenance);
      if (mode_entree == 7) {
        provenance.addClassName('notNull');
        $('labelFor_editRPU__provenance').addClassName('notNull');
      }
      else {
        provenance.removeClassName('notNull');
        $('labelFor_editRPU__provenance').removeClassName('notNull');
      }
    {{/if}}
  }

  function loadServiceMutation(mode_entree){
    $('service_entree_mutation').setVisible(mode_entree == 6);
  }

  function printDossier(id) {
    var url = new Url("urgences", "print_dossier");
    url.addParam("rpu_id", id);
    url.popup(700, 550, "RPU");
  }

  function loadResultLabo(sejour_id) {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('Imeds');
  }

  function loadSuiviClinique(sejour_id) {
    var url = new Url("soins", "ajax_vw_suivi_clinique");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("suivi_clinique");
  }

  function requestInfoPat() {
    var oForm = getForm("editRPU");
    var iPatient_id = $V(oForm._patient_id);
    if(!iPatient_id){
      return false;
    }
    var url = new Url("patients", "httpreq_get_last_refs");
    url.addParam("patient_id", iPatient_id);
    url.addParam("is_anesth", 0);
    url.addParam("show_dhe_ecap", '{{$show_buttons_urgence}}');
    url.requestUpdate("infoPat");
    return true;
  }

  addScroll = function() {
    var content = $("content-rpu");
    var header = $("header-rpu");
    if (content && header) {
      ViewPort.SetAvlHeight('content-rpu', 1.0);
      content.on('scroll', function() {
        header.setClassName('shadow', content.scrollTop);
      });
    }
  }

  function printEtiquettes() {
    var nb_printers = {{$nb_printers|@json}};
    if (nb_printers > 0) {
      var url = new Url('compteRendu', 'ajax_choose_printer');
      url.addParam('mode_etiquette', 1);
      url.addParam('object_class', '{{$rpu->_class}}');
      url.addParam('object_id', '{{$rpu->_id}}');
      url.requestModal(400);
    }
    else {
      getForm('download_etiq').submit();
    }
  }

  showDossierSoins = function(sejour_id, date, default_tab){
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modal", 1);
    if(default_tab){
      url.addParam("default_tab", default_tab);
    }
    url.requestModal("95%", "90%", {
      showClose: false
    });
    modalWindow = url.modalObject;
  };

  updateModeEntree = function(select) {
    var selected = select.options[select.selectedIndex];
    var form = select.form;
    $V(form.elements.mode_entree, selected.get("mode"));
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

  Main.add(function () {
    {{if $rpu->_id && $can->edit}}
      if (window.DossierMedical){
        DossierMedical.reloadDossierPatient();
      }
      var tab_sejour = Control.Tabs.create('tab-dossier', false, {
        afterChange: function(container) {
          switch (container.id) {
            case 'suivi_clinique':
              loadSuiviClinique('{{$rpu->sejour_id}}');
              break;
            {{if $rpu->sejour_id}}
              case 'prescription_sejour':
                Prescription.reloadPrescSejour('', '{{$rpu->sejour_id}}','', '', null, null, null,'');
                break;
              case 'dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}':
                PlanSoins.loadTraitement('{{$rpu->sejour_id}}',null,'','administration');
                {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
                  loadSuiviLite();
                {{/if}}
                break;
            {{/if}}
            case 'dossier_suivi':
              loadSuivi({{$rpu->sejour_id}});
              break;
            case 'constantes-medicales':
              refreshConstantesHack('{{$rpu->sejour_id}}');
              break;
            case 'examens':
              showExamens('{{$consult->_id}}');
              break;
            case 'actes':
              loadActes('{{$rpu->sejour_id}}');
              break;
            case 'Imeds':
              loadResultLabo('{{$rpu->sejour_id}}');
              break;
            case 'doc-items':
              loadDocItems('{{$rpu->sejour_id}}', '{{$consult->_id}}');
              break;
          }
        }
      });
    {{/if}}

    {{if $isPrescriptionInstalled}}
      Prescription.hide_header = true;
    {{/if}}

    {{if "forms"|module_active}}
      if ($("ex-forms-rpu")) {
        ExObject.loadExObjects("{{$rpu->_class}}", "{{$rpu->_id}}", "ex-forms-rpu", 0.5);
      }
    {{/if}}

    if (document.editAntFrm){
      document.editAntFrm.type.onchange();
    }

    addScroll();
  });

</script>

<form name="download_etiq" style="display: none;" action="?" target="_blank" method="get" class="prepared">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="a" value="print_etiquettes" />
  <input type="hidden" name="object_id" value="{{$rpu->_id}}" />
  <input type="hidden" name="object_class" value="{{$rpu->_class}}" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="dialog" value="1" />
</form>

{{if !$rpu->_id}}
  {{mb_include module=urgences template=inc_aed_rpu}}
  {{mb_return}}
{{/if}}

<!-- Dossier Médical du patient -->
{{if $can->edit}}
  {{assign var=consult value=$rpu->_ref_consult}}

  {{if $rpu->mutation_sejour_id}}
    {{mb_include module="urgences" template="inc_aed_rpu"}}
    <div class="small-info">
      Une mutation du séjour a été effectuée, il est possible de visualiser le dossier de soins en cliquant sur le bouton suivant
      <button type="button" class="search" onclick="showDossierSoins('{{$rpu->mutation_sejour_id}}');">{{tr}}soins.button.Dossier-soins{{/tr}}</button>
    </div>
  {{else}}
    <div id="header-rpu" style="position: relative;">
      <div id="patient-banner">
        {{mb_include module=soins template=inc_patient_banner}}
      </div>

      <ul id="tab-dossier" class="control_tabs">
        <li><a href="#rpu">{{tr}}soins.tab.rpu{{/tr}}</a></li>

        <li><a href="#suivi_clinique">{{tr}}soins.tab.synthese{{/tr}}</a></li>

        <li><a href="#antecedents">{{tr}}soins.tab.antecedent_and_treatment{{/tr}}</a></li>

        <li>
          <a href="#constantes-medicales">{{tr}}soins.tab.surveillance{{/tr}}</a>
        </li>

        {{if $isPrescriptionInstalled && $modules.dPprescription->_can->read && !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
          <li><a href="#dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}">{{tr}}soins.tab.suivi_soins{{/tr}}</a></li>
          <li><a href="#prescription_sejour">{{tr}}soins.tab.prescription{{/tr}}</a></li>
        {{else}}
          <li><a href="#dossier_suivi">{{tr}}soins.tab.suivi_soins{{/tr}}</a></li>
        {{/if}}

        {{if $app->user_prefs.ccam_sejour == 1 }}
          <li><a href="#actes">{{tr}}soins.tab.cotation-infirmiere{{/tr}}</a></li>
        {{/if}}

        {{if "dPImeds"|module_active}}
          <li><a href="#Imeds">{{tr}}soins.tab.labo{{/tr}}</a></li>
        {{/if}}

        <li>
          <a href="#examens">{{tr}}soins.tab.dossier-medical{{/tr}}</a>
        </li>

        {{if "forms"|module_active}}
          <li><a href="#ex-forms-rpu">{{tr}}soins.tab.formulaires{{/tr}}</a></li>
        {{/if}}

        <li><a href="#doc-items">{{tr}}soins.tab.documents{{/tr}}</a></li>

      </ul>
    </div>

    <div id="content-rpu">
      <div id="rpu">
        {{mb_include module="urgences" template="inc_aed_rpu"}}
        <table style="width: 100%;" class="tbl">
          <tr>
            <th class="category">Attentes</th>
            <th class="category">Prise en charge médicale</th>
          </tr>

          <tr>
            <td style="width: 60%">
              {{mb_include module="urgences" template="inc_vw_rpu_attente"}}
            </td>
            <td class="button {{if $sejour->type != "urg"  && !$sejour->UHCD}}arretee{{/if}}">
              {{mb_include module="urgences" template="inc_pec_praticien"}}
            </td>
          </tr>
        </table>
      </div>

      <div id="suivi_clinique" style="display: none"></div>

      <div id="antecedents" style="display: none">
        {{assign var="current_m"  value="dPurgences"}}
        {{assign var="_is_anesth" value="0"}}
        {{assign var=sejour_id    value=""}}

        {{if $patient->_ref_dossier_medical && $patient->_ref_dossier_medical->_id && !$patient->_ref_dossier_medical->_canEdit}}
          {{mb_include module=dPpatients template=CDossierMedical_complete object=$patient->_ref_dossier_medical}}
        {{else}}
          {{mb_include module=cabinet template=inc_ant_consult chir_id=$app->user_id show_header=0}}
        {{/if}}
      </div>

      <div id="constantes-medicales" style="display:none"></div>
      <div id="ex-forms-rpu" style="display: none"></div>

      <div id="examens"    style="display:none">
        <div class="small-info">
          Aucune prise en charge médicale
        </div>
      </div>

      {{if $app->user_prefs.ccam_sejour == 1 }}
        <div id="actes" style="display: none;"></div>
      {{/if}}

      {{if $isPrescriptionInstalled && $modules.dPprescription->_can->read && !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
        <div id="prescription_sejour" style="display: none;">
          <div class="small-info">
            Aucune prescription
          </div>
        </div>
        <div id="dossier_traitement{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}_compact{{/if}}">
          {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
            {{mb_include module=soins template=inc_dossier_soins_widgets}}
          {{/if}}
          <div class="small-info">
            Aucun plan de soins
          </div>
        </div>
      {{else}}
        <div id="dossier_suivi" style="display:none"></div>
      {{/if}}

      {{if "dPImeds"|module_active}}
        <div id="Imeds" style="display: none;"></div>
      {{/if}}

      <div id="doc-items" style="display: none;"></div>
    </div>
  {{/if}}
{{/if}}


{{if $sejour->mode_entree}}
  <script>
    // Lancement des fonctions de contraintes entre les champs
    ContraintesRPU.updateProvenance("{{$sejour->mode_entree}}");
  </script>
{{/if}}

{{if $conf.ref_pays == 2}}
  </div>
{{/if}}
