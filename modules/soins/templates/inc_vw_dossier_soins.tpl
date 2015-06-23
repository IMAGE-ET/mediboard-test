{{* $Id: inc_vw_dossier_soins.tpl 14528 2012-02-02 14:52:03Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 14528 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$sejour->_id}}
  <div class="small-info">
    Veuillez sélectionner un séjour pour pouvoir accéder au suivi de soins.
  </div>
  {{mb_return}}
{{/if}}

<div class="small-error" id="sejour-load-error" style="display: none;">Une erreur s'est produite durant le chargement du dossier, veuillez réessayer.</div>

<script>
  addManualPlanification = function(date, time, key_tab, object_id, object_class, original_date, quantite){
    var prise_id = !isNaN(key_tab) ? key_tab : '';
    var unite_prise = isNaN(key_tab) ? key_tab : '';

    var oForm = document.addPlanif;
    $V(oForm.administrateur_id, '{{$app->user_id}}');
    $V(oForm.object_id, object_id);
    $V(oForm.object_class, object_class);
    $V(oForm.unite_prise, unite_prise);
    $V(oForm.prise_id, prise_id);
    $V(oForm.quantite, quantite);

    var dateTime = date+" "+time;

    $V(oForm.dateTime, dateTime);
    $V(oForm.original_dateTime, original_date);

    onSubmitFormAjax(oForm, function() {
      PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}',PlanSoins.nb_decalage, 'planification', object_id, object_class, key_tab);
    } );
  };

  refreshDossierSoin = function(mode_dossier, chapitre, force_refresh) {
    PlanSoins.toggleAnciennete(chapitre);
    if(!window[chapitre+'SoinLoaded'] || force_refresh) {
      PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}',PlanSoins.nb_decalage, mode_dossier, null, null, null, chapitre, null, null, '{{$hide_old_lines}}', '{{$hide_line_inactive}}');
      window[chapitre+'SoinLoaded'] = true;
    }
  };

  addCibleTransmission = function(sejour_id, object_class, object_id, libelle_ATC) {
    addTransmission(sejour_id, '{{$app->user_id}}', null, object_id, object_class, libelle_ATC);
  };


  editPerf = function(prescription_line_mix_id, date, mode_dossier, sejour_id) {
    var url = new Url("prescription", "edit_perf_dossier_soin");
    url.addParam("prescription_line_mix_id", prescription_line_mix_id);
    url.addParam("date", date);
    url.addParam("mode_dossier", mode_dossier);
    url.addParam("sejour_id", sejour_id);
    url.popup(800,600,"Pefusion");
  };

  submitPosePerf = function(oFormPerf) {
    if (confirm('Etes vous sur de vouloir poser la perfusion ?')) {
      $V(oFormPerf.date_pose, 'current');
      $V(oFormPerf.time_pose, 'current');
      onSubmitFormAjax(oFormPerf, function() {
        PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}', PlanSoins.nb_decalage,'{{$mode_dossier}}',oFormPerf.prescription_line_mix_id.value,'CPrescriptionLineMix','');
        PlanSoins.refreshPauseRetrait(oFormPerf.prescription_line_mix_id.value);
      } );
    }
  };

  submitRetraitPerf = function(oFormPerf){
    if (confirm('Etes vous sur de vouloir retirer définitivement la perfusion ?')) {
      $V(oFormPerf.date_retrait, 'current');
      $V(oFormPerf.time_retrait, 'current');
      onSubmitFormAjax(oFormPerf, function() {
        PlanSoins.loadTraitement('{{$sejour->_id}}', '{{$date}}', PlanSoins.nb_decalage, '{{$mode_dossier}}', oFormPerf.prescription_line_mix_id.value, 'CPrescriptionLineMix', '');
        PlanSoins.refreshPauseRetrait(oFormPerf.prescription_line_mix_id.value);
      });
    }
  };

  viewLegend = function() {
    var url = new Url("dPhospi", "vw_legende_dossier_soin");
    url.modal({width: 400, height: 200});
  };

  calculSoinSemaine = function(date, prescription_id){
    var url = new Url("prescription", "httpreq_vw_dossier_soin_semaine");
    url.addParam("date", date);
    url.addParam("prescription_id", prescription_id);
    url.requestUpdate("semaine");
  };

  viewSegments = function(chapitre, line_guid){
    var url = new Url("prescription", "vw_segments_line");
    url.addParam("line_guid", line_guid);
    url.requestModal(500, null, { onClose: function() {
      if (window.refreshDossierSoin) {
        refreshDossierSoin('',chapitre, true);
      }
      if (window.updatePlanSoinsPatients) {
        updatePlanSoinsPatients();
      }} } );
  };

  // Initialisation
  window.periodicalBefore = null;
  window.periodicalAfter = null;

  tabs = null;

  refreshTabState = function(){
    window['medSoinLoaded'] = false;
    window['perfusionSoinLoaded'] = false;
    window['oxygeneSoinLoaded'] = false;
    window['aerosolSoinLoaded'] = false;
    window['alimentationSoinLoaded'] = false;
    window['preparationSoinLoaded'] = false;
    window['inscriptionSoinLoaded'] = false;
    window['all_medSoinLoaded'] = false;
    window['all_chapsSoinLoaded'] = false;

    window['injSoinLoaded'] = false;
    {{if "dPprescription"|module_active}}
      {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
      {{foreach from=$specs_chapitre->_list item=_chapitre}}
        window['{{$_chapitre}}SoinLoaded'] = false;
      {{/foreach}}
    {{/if}}
    if(tabs){
      if(tabs.activeLink){
        tabs.activeLink.up().onmouseup();
      } else {
        if($('tab_categories') && $('tab_categories').down()){
          $('tab_categories').down().onmousedown();
          tabs.setActiveTab($('tab_categories').down().down().key);
        }
      }
    }
  };

  updateTasks = function(sejour_id){
    var url = new Url("soins", "ajax_vw_tasks_sejour");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("tasks");
  };

  showDebit = function(div, color){
    $(div.up('tr').up('tr')).select("."+div.down().className).each(function(elt){
      elt.setStyle( { backgroundColor: '#'+color } );
    });
  };

  submitSuivi = function(oForm, del) {
    sejour_id = oForm.sejour_id.value;
    submitFormAjax(oForm, 'systemMsg', { onComplete: function() {
      if (!del) {
        Control.Modal.close();
      }
      if($V(oForm.object_class)|| $V(oForm.libelle_ATC)){
        // Refresh de la partie administration
        if($('jour').visible()){
          PlanSoins.loadTraitement(sejour_id,'{{$date}}','','administration');
        }
        // Refresh de la partie plan de soin
        if($('semaine').visible()){
          {{if isset($prescription_id|smarty:nodefaults)}}
          calculSoinSemaine('{{$date}}', '{{$prescription_id}}');
          {{/if}}
        }
      }
      if ($('dossier_suivi').visible()) {
        loadSuivi(sejour_id);
      }
      updateNbTrans(sejour_id);
    } });
  };

  openSurveillanceTab = function() {
    var elt = $$('a[href="#constantes-medicales"]')[0];
    elt.click();
  };

  openModalTP = function() {
    window.modalUrlTp = new Url("prescription", "ajax_vw_traitements_personnels");
    window.modalUrlTp.addParam("object_guid", '{{$prescription->_ref_object->_guid}}');
    window.modalUrlTp.requestModal("80%", "80%", {onClose: loadSuiviSoins });
  };

  // Cette fonction est dupliquée
  function updateNbTrans(sejour_id) {
    var url = new Url("hospi", "ajax_count_transmissions");
    url.addParam("sejour_id", sejour_id);
    url.requestJSON(function(count)  {
      Control.Tabs.setTabCount('dossier_suivi', count);
    });
  };

  createConsult = function() {
    {{if $app->_ref_user->isAnesth()}}
    bindOperation('{{$sejour->_id}}');
    {{else}}
    onSubmitFormAjax(getForm('addConsultation'));
    {{/if}}
  };

  createConsultEntree = function() {
    var form = getForm('addConsultation');
    $V(form.type, 'entree');
    onSubmitFormAjax(getForm('addConsultation'));
  };

  toggleLockCible = function(transmission_id, lock) {
    var form = getForm("lockTransmission");
    $V(form.transmission_medicale_id, transmission_id);
    $V(form.locked, lock);
    onSubmitFormAjax(form, {onComplete: function() {
      loadSuivi('{{$sejour->_id}}');
    }});
  };

  showTrans = function(transmission_id, from_compact) {
    var url = new Url("hospi", "ajax_list_locked_trans");
    url.addParam("transmission_id", transmission_id);
    url.addParam("from_compact", from_compact);
    url.requestModal(850, null, {maxHeight: '550'});
  };

  mergeTrans = function(transmissions_ids) {
    var url = new Url("system", "object_merger");
    url.addParam("objects_class", "CTransmissionMedicale");
    url.addParam("objects_id", transmissions_ids);
    url.popup(800, 600, "merge_transmissions");
  };

  onMergeComplete = function() {
    loadSuivi('{{$sejour->_id}}');
  };

  modalConsult = function(consult_id) {
    var url = new Url("cabinet", "ajax_short_consult");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.addParam("consult_id", consult_id);
    url.modal(600, 400);
    url.modalObject.observe("afterClose", function() {
      if (window.loadSuivi) {
        loadSuivi("{{$sejour->_id}}");
      }
    });
  };

  compteurAlertesObs = function() {
    var url = new Url("hospi", "ajax_count_alert_obs", "raw");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.requestJSON(function(count) {
      var span_ampoule = $('span-alerts-medium-observation-{{$sejour->_guid}}');
      if (span_ampoule) {
        if (count) {
          span_ampoule.show();
          span_ampoule.down('span').innerHTML = count;
        }
        else {
          span_ampoule.hide();
        }
      }
    });
  }

  {{if "dPprescription"|module_active}}
    prescriptions_ids = {{$multiple_prescription|@json}};
  {{/if}}

  Main.add(function () {
    if (window.currentSejourId && window.currentSejourId != '{{$sejour->_id}}') {
      $("sejour-load-error").show();
      return;
    }

    PlanSoins.anciennete = {{"dPprescription general alerte_refresh_plan"|conf:"CGroups-$g"}}

    {{if !"dPprescription"|module_active || $multiple_prescription|@count <= 1}}
      {{if "dPprescription"|module_active}}
        PlanSoins.init({
          composition_dossier: {{$composition_dossier|@json}},
          date: "{{$date}}",
          manual_planif: "{{$manual_planif}}",
          bornes_composition_dossier:  {{$bornes_composition_dossier|@json}},
          nb_postes: {{$bornes_composition_dossier|@count}},
          nb_decalage: {{$nb_decalage}},
          plan_soin_id: 'plan_soin'
        });
      {{/if}}

      // Deplacement du dossier de soin
      if($('plan_soin')){
        PlanSoins.moveDossierSoin($('tbody_date'));
      }

      {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
        $('jour').show();
        $('semaine').hide();
        $('tab_dossier_soin').down('li.jour').onmousedown();
      {{/if}}
      {{if "dPprescription general show_perop_suivi_soins"|conf:"CGroups-$g" || !"soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
        var tab_dossier_soin = Control.Tabs.create('tab_dossier_soin', true);
        tab_dossier_soin.activeLink.up('li').onmousedown();
      {{/if}}

      if($('tab_categories')){
        tabs = Control.Tabs.create('tab_categories', true);
      }
      refreshTabState();

      document.observe("mousedown", function(e){
       // fait crasher IE quand ouvert dans une iframe dans un showModalDialog:
       // l'evenement est lancé sur la balise html et ca plante
        try {
          if (!Event.element(e).up("div.tooltip")){
            $$("div.tooltip").invoke("hide");
          }
        } catch(e) {}
      });

      var options = {
          minHours: '0',
          maxHours: '9'
        };

      var dates = {};
      dates.limit = {
        start: '{{$sejour->entree|date_format:"%Y-%m-%d"}}',
        stop: '{{$sortie_sejour|date_format:"%Y-%m-%d"}}'
      };

      var oFormDate = getForm("changeDateDossier");
      if (oFormDate) {
        Calendar.regField(oFormDate.date, dates, {noView: true});
      }
    {{/if}}
  });
</script>

<form name="movePlanifs" action="" method="post">
  <input type="hidden" name="dosql" value="do_move_planifs_aed" />
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="" />
  <input type="hidden" name="prise_id" value="" />
  <input type="hidden" name="datetime" value="" />
  <input type="hidden" name="nb_hours" value="" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="unite_prise" value="" />
  <input type="hidden" name="type_move" value="" />
</form>

<form name="lockTransmission" method="post" action="?">
  <input type="hidden" name="m" value="hospi"/>
  <input type="hidden" name="dosql" value="do_transmission_aed"/>
  <input type="hidden" name="transmission_medicale_id" />
  <input type="hidden" name="locked" value="1" />
</form>

<form name="addTransmissionFrm" method="post" action="?">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_transmission_aed" />
  <input type="hidden" name="object_id" />
  <input type="hidden" name="object_class" />
  <input type="hidden" name="text" />
  <input type="hidden" name="user_id" value="{{$app->_ref_user->_id}}" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
</form>

<form name="addConsultation" method="post" action="?">
  <input type="hidden" name="m" value="cabinet" />
  <input type="hidden" name="dosql" value="do_consult_now" />
  <input type="hidden" name="_prat_id" value="{{$app->_ref_user->_id}}" />
  <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_operation_id" value="" />
  <input type="hidden" name="type" value="" />
  <input type="hidden" name="_in_suivi" value="1" />
  <input type="hidden" name="callback" value="modalConsult" />
</form>

{{if "dPprescription"|module_active && $multiple_prescription|@count > 1}}
  <div class="big-error">
    {{tr}}CPrescription.merge_prescription_message{{/tr}}
    <br/>
    {{if $admin_prescription}}
    <button class="hslip" onclick="Prescription.mergePrescriptions(prescriptions_ids)">Fusionner les prescriptions</button>
    {{else}}
      Veuillez contacter un praticien ou un administrateur pour effectuer cette fusion.
    {{/if}}
  </div>
  {{mb_return}}
{{/if}}

<form name="adm_multiple" action="?" method="get">
  <input type="hidden" name="_administrations">
  <input type="hidden" name="_administrations_mix">
</form>

<form name="addPlanif" action="" method="post">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administration_id" value="" />
  <input type="hidden" name="planification" value="1" />
  <input type="hidden" name="administrateur_id" value="" />
  <input type="hidden" name="dateTime" value="" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="unite_prise" value="" />
  <input type="hidden" name="prise_id" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="" />
  <input type="hidden" name="original_dateTime" value="" />
</form>

<form name="addPlanifs" action="" method="post">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_administrations_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="decalage" value="" />
  <input type="hidden" name="administrations_ids" value=""/>
  <input type="hidden" name="planification" value="1" />
</form>

<form name="addManualPlanifPerf" action="" method="post">
  <input type="hidden" name="dosql" value="do_planif_line_mix_aed" />
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
  <input type="hidden" name="datetime" value="" />
  <input type="hidden" name="planification_systeme_id" value="" />
  <input type="hidden" name="prescription_line_mix_id" value="" />
  <input type="hidden" name="original_datetime" value="" />
</form>

<ul id="tab_dossier_soin" class="control_tabs" style="text-align: left; {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}border-bottom: none;{{/if}}">
  {{if "dPprescription"|module_active}}
    <!-- Plan de soins journée -->
    <li onmousedown="refreshTabState();" {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g" && !"dPprescription general show_perop_suivi_soins"|conf:"CGroups-$g"}}style="display: none;"{{/if}}
        class="jour">
      <a href="#jour">{{tr}}Soin-tabSuivi-tabViewDay{{/tr}}</a>
    </li>

    <!-- Plan de soins semaine -->
    <li onmousedown="calculSoinSemaine('{{$date}}','{{$prescription_id}}');" {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}style="display: none;"{{/if}} class="semaine">
      <a href="#semaine">{{tr}}Soin-tabSuivi-tabViewWeek{{/tr}}</a>
    </li>
    {{if "dPprescription general show_perop_suivi_soins"|conf:"CGroups-$g"}}
      <li onmousedown="PlanSoins.showPeropAdministrations('{{$prescription_id}}')">
        <a href="#perop_adm" {{if $count_perop_adm == 0}}class="empty"{{/if}}>
          Perop {{if $count_perop_adm}}<small>({{$count_perop_adm}})</small>{{/if}}
        </a>
      </li>
    {{/if}}
  {{/if}}

  <!-- Tâches -->
  <li onmousedown="updateTasks('{{$sejour->_id}}');" {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}style="display: none;"{{/if}}>
    <a href="#tasks">
      Tâches
      <small>(&ndash; / &ndash;)</small>
      <script>
      Control.Tabs.setTabCount('tasks', {{$sejour->_count_pending_tasks}}, {{$sejour->_count_tasks}});
      </script>
    </a>
  </li>

  <!-- Transmissions -->
  <li onmousedown="loadSuivi('{{$sejour->_id}}')" {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}style="display: none;"{{/if}}>
    <a href="#dossier_suivi">
      Trans. <small id="nb_trans"></small> / Obs
      {{if !"soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g" && "soins Observations manual_alerts"|conf:"CGroups-$g"}}
        {{mb_include module=system template=inc_icon_alerts object=$sejour tag=observation callback="function() { compteurAlertesObs(); loadSuivi('`$sejour->_id`'); }" show_empty=1 show_span=1 event=onmouseover}}
      {{/if}} . / Consult.
      {{if "soins CConstantesMedicales constantes_show"|conf:"CGroups-$g"}}/ Const.{{/if}}
    </a>
  </li>
</ul>

{{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
  <hr />
{{/if}}

<span style="float: right;">
  {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
    <button type="button" class="search" onclick="PlanSoins.showModalTasks('{{$sejour->_id}}');">Tâches</button>
  {{/if}}
  <button type="button" class="print"
          onclick="{{if isset($prescription|smarty:nodefaults)}}Prescription.printOrdonnance('{{$prescription->_id}}');{{/if}}">
    Ordonnance
  </button>
</span>

<div id="jour" style="display:none">
  {{if "soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}
      <button type="button" class="change" onclick="PlanSoins.toggleView('semaine');" style="float: right">Vue semaine</button>
    {{/if}}
    {{if "dPprescription"|module_active && $prescription_id}}
    {{assign var=borne_inf value="CMbDT::date"|static_call:"-1 day":$sejour->_entree|@date_format:"%Y-%m-%d"}}
    {{assign var=borne_sup value="CMbDT::date"|static_call:"+1 day":$sejour->_sortie|@date_format:"%Y-%m-%d"}}

    {{if "soins suivi hide_old_line"|conf:"CGroups-$g" && $date == $smarty.now|date_format:'%Y-%m-%d'}}
      {{if $hide_old_lines}}
        <button type="button" class="search" style="float: right;{{if !"soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}margin-top: -4px;{{/if}}" onclick="PlanSoins.reloadSuiviSoin('{{$sejour->_id}}', '{{$date}}', 0, '{{$hide_line_inactive}}');">
          Afficher les prescriptions terminées ({{$hidden_lines_count}})
        </button>
      {{else}}
        <button type="button" class="search" style="float: right;{{if !"soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}margin-top: -4px;{{/if}}" onclick="PlanSoins.reloadSuiviSoin('{{$sejour->_id}}', '{{$date}}', 1, '{{$hide_line_inactive}}');">
          Masquer les prescriptions terminées
        </button>
      {{/if}}
    {{/if}}

    {{if $app->user_prefs.hide_line_inactive && $date == $smarty.now|date_format:'%Y-%m-%d'}}
      {{if $hide_line_inactive}}
        <button type="button" class="search" style="float: right;{{if !"soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}margin-top: -4px;{{/if}}" onclick="PlanSoins.reloadSuiviSoin('{{$sejour->_id}}', '{{$date}}', '{{$hide_old_lines}}', 0);">
          Afficher les lignes inactives ({{$hide_inactive_count}})
        </button>
      {{else}}
        <button type="button" class="search" style="float: right;{{if !"soins Other vue_condensee_dossier_soins"|conf:"CGroups-$g"}}margin-top: -4px;{{/if}}" onclick="PlanSoins.reloadSuiviSoin('{{$sejour->_id}}', '{{$date}}', '{{$hide_old_lines}}', 1);">
          Masquer les lignes inactives
        </button>
      {{/if}}
    {{/if}}

    <h2 style="text-align: center;">
          <button type="button"
                 class="left notext {{if $date <= $borne_inf}}opacity-50{{/if}}"
                 {{if $date > $borne_inf}}onclick="PlanSoins.loadTraitement('{{$sejour->_id}}','{{$prev_date}}', null, null, null, null, null, null, '1', '{{$hide_close}}');"{{/if}}
                 ></button>

       Plan de soins du {{$date|@date_format:"%d/%m/%Y"}}

       {{foreach from=$prescription->_jour_op item=_info_jour_op}}
         (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
       {{/foreach}}
       <form name="changeDateDossier" method="get" action="?" onsubmit="return false" style="font-size: 11px">
         <input type="hidden" name="date" class="date" value="{{$date}}" onchange="PlanSoins.loadTraitement('{{$sejour->_id}}',this.value,'','administration', null, null, null, null, '1', '{{$hide_close}}');"/>
       </form>
       <button type="button"
               class="right notext {{if $date >= $borne_sup}}opacity-50{{/if}}"
               {{if $date < $borne_sup}}onclick="PlanSoins.loadTraitement('{{$sejour->_id}}','{{$next_date}}','','administration', null, null, null, null, '1', '{{$hide_close}}');"{{/if}}
               ></button>
    </h2>

    <table style="width: 100%">
      <tr>
        <td>
          <button type="button" class="search"
                  onclick="PlanSoins.regroup_lines = !{{$regroup_lines}};
                    PlanSoins.loadTraitement('{{$prescription->object_id}}', PlanSoins.date, null, null, null, null, null, null, '1')">
            Somme
          </button>

          <button type="button" class="print" onclick="PlanSoins.printBons('{{$prescription_id}}');" title="{{tr}}Print{{/tr}}">
            Bons
          </button>
          {{if $conf.dPmedicament.base == "bcb"}}
          <button type="button" class="print" onclick="Prescription.viewFullAlertes('{{$prescription_id}}')" title="{{tr}}Print{{/tr}}">
            Alertes
          </button>
          {{/if}}
          <button type="button" class="tick" onclick="PlanSoins.applyAdministrations();" id="button_administration">
          <button type="button" class="new" onclick="openModalTP()">
            Gestion des traitements personnels ({{$prescription->_count_lines_tp}}/{{$prescription->_count_dossier_medical_tp}})</button>
          </button>
        </td>
        <td style="text-align: center">
          <form name="mode_dossier_soin" action="?" method="get">
            <label>
              <input type="radio" name="mode_dossier" value="administration" {{if $mode_dossier == "administration" || $mode_dossier == ""}}checked="checked"{{/if}}
                     onclick="PlanSoins.viewDossierSoin($('plan_soin'));"/>Administration
            </label>
            <label>
              <input type="radio" name="mode_dossier" value="planification" {{if $mode_dossier == "planification"}}checked="checked"{{/if}}
                     onclick="PlanSoins.viewDossierSoin($('plan_soin'));" />Planification
            </label>
         </form>
        </td>
        <td style="text-align: right">
          <button type="button" class="search" onclick="viewLegend();">Légende</button>
        </td>
      </tr>
    </table>

    {{assign var=transmissions value=$prescription->_transmissions}}
    {{assign var=count_recent_modif value=$prescription->_count_recent_modif}}
    {{assign var=count_urgence value=$prescription->_count_urgence}}
    <table class="main">
      <tr>
        {{assign var=horizontal_chapters value="soins suivi horizontal_chapters"|conf:"CGroups-$g"}}

        {{if !$horizontal_chapters}}
        <td style="white-space: nowrap;" class="narrow">
          <!-- Affichage des onglets du dossier de soins -->
          {{mb_include module="prescription" template="inc_vw_tab_dossier_soins"}}
        </td>
        {{/if}}
        <td>
          {{if $horizontal_chapters}}
            {{mb_include module="prescription" template="inc_vw_tab_dossier_soins"}}
          {{/if}}
          <!-- Affichage du contenu du dossier de soins -->
          {{mb_include module="prescription" template="inc_vw_content_dossier_soins"}}
        </td>
      </tr>
    </table>
  {{else}}
    <div class="small-info">
    Ce dossier ne possède pas de prescription de séjour
    </div>
  {{/if}}
</div>

<div id="semaine" style="display:none"></div>
{{if "dPprescription general show_perop_suivi_soins"|conf:"CGroups-$g"}}
  <div id="perop_adm" style="display: none"></div>
{{/if}}
<div id="tasks" style="display:none"></div>
<div id="dossier_suivi" style="display:none"></div>