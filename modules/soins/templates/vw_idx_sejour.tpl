{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=dPfiles script=file_category}}

{{if $conf.dPhospi.CLit.alt_icons_sortants}}
  {{assign var=suffixe_icons value="2"}}
{{else}}
  {{assign var=suffixe_icons value=""}}
{{/if}}

{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

<script>
  function paramUserSejour(sejour_id) {
    var url = new Url("planningOp", "vw_affectations_sejour");
    url.addParam("sejour_id",sejour_id);
    url.requestModal(null, null, {
      onClose : function() {
        getForm("selService").submit();
      }});
  }

  function popEtatSejour(sejour_id) {
    var url = new Url("hospi", "vw_parcours");
    url.addParam("sejour_id",sejour_id);
    url.pop(1000, 650, 'Etat du Séjour');
  }

  function addSejourIdToSession(sejour_id) {
    var url = new Url("system", "httpreq_set_value_to_session");
    url.addParam("module","{{$m}}");
    url.addParam("name","sejour_id");
    url.addParam("value",sejour_id);
    url.requestUpdate("systemMsg");
  }

  function loadViewSejour(sejour_id, date, elt, tab) {
    var url = new Url('soins', 'ajax_vw_dossier_sejour');
    url.addParam('sejour_id', sejour_id);
    url.addParam('date', date);

    if (tab) {
      url.addParam('default_tab', tab);
    }
    else {
      var selected_tab = $$('ul#tab-sejour li a.active');
      if (selected_tab.length == 1) {
        url.addParam('default_tab', selected_tab[0].href.split("#")[1]);
      }
    }

    url.requestUpdate('dossier_sejour', function() {
      addSejourIdToSession(sejour_id);
      markAsSelected(elt);
    });
  }

  function printPatient(patient_id) {
    var url = new Url("dPpatients", "print_patient");
    url.addParam("patient_id", patient_id);
    url.popup(700, 550, "Patient");
  }

  function updatePatientsListHeight() {
    var vpd = document.viewport.getDimensions(),
        scroller = $("left-column").down(".scroller"),
        pos = scroller.cumulativeOffset();
    scroller.setStyle({height: (vpd.height - pos[1] - 6)+'px'});
  }

  function compteurAlerte(level, prescription_guid) {
    var url = new Url("prescription", "ajax_count_alerte", "raw");
    url.addParam("prescription_guid", prescription_guid);
    url.requestJSON(function(count) {
      var span_ampoule = $('span-icon-alert-'+level+'-'+prescription_guid);
      if (count[level]) {
        span_ampoule.down('span').innerHTML = count[level];
      }
      else {
        span_ampoule.down('span').remove();
        span_ampoule.down('img').remove();
      }
    });
  }

  Main.add(function () {
    Calendar.regField(getForm("changeDate").date, null, {noView: true});

    updatePatientsListHeight();

    Event.observe(window, "resize", updatePatientsListHeight);

    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}

    {{if $app->user_prefs.show_file_view}}
      FilesCategory.showUnreadFiles();
    {{/if}}
  });

  function markAsSelected(element) {
    element.up("tr").addUniqueClassName("selected");
  }

  viewBilanService = function(service_id, date) {
    var url = new Url("hospi", "vw_bilan_service");
    url.addParam("service_id", service_id);
    url.addParam("date", date);
    url.popup(800,500,"Bilan par service");
  }

  checkAnesth = function(oField) {
    // Recuperation de la liste des anesthésistes
    var anesthesistes = {{$anesthesistes|@json}};

    var oForm = getForm("selService");
    var praticien_id = $V(oForm.praticien_id);
    var service_id   = $V(oForm.service_id);

    if (oField.name == "service_id"){
      if (anesthesistes.include(praticien_id)) {
        $V(oForm.praticien_id, '', false);
      }
    }

    if (oField.name == "praticien_id"){
      if (anesthesistes.include(praticien_id)) {
        $V(oForm.service_id, '', false);
      }
    }
  }

  savePref = function(form) {
    var formPref = getForm('editPrefServiceSoins');
    var formService = getForm('selService');
    var service_id = $V(form.default_service_id);

    var default_service_id_elt = formPref.elements['pref[default_services_id]'];
    var default_service_id = $V(default_service_id_elt).evalJSON();
    default_service_id.g{{$g}} = service_id;
    $V(default_service_id_elt, Object.toJSON(default_service_id));
    return onSubmitFormAjax(formPref, function() {
      Control.Modal.close();
      $V(formService.service_id, service_id);
    });
  }

</script>

<form name="form_prescription" action="" method="get">
  <input type="hidden" name="sejour_id" value="{{$object->_id}}" />
</form>
      
<table class="main">
  <tr>
    <td>
      <table class="form" id="left-column" style="width:240px;">
        <tr>
          <th class="title">

            <form name="editPrefVueSejour" method="post" style="float: left">
              <input type="hidden" name="m" value="admin" />
              <input type="hidden" name="dosql" value="do_preference_aed" />
              <input type="hidden" name="user_id" value="{{$app->user_id}}" />
              <input type="hidden" name="pref[vue_sejours]" value="global" />
              <input type="hidden" name="postRedirect" value="m=soins&tab=vw_sejours" />
              <button type="submit" class="change notext">Vue par défaut</button>
            </form>

            {{$date|date_format:$conf.longdate}}
            <form action="?" name="changeDate" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="tab" value="{{$tab}}" />
              <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
            </form>
          </th>
        </tr>
        
        <tr>
          <td>
            <form name="selService" action="?" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="tab" value="{{$tab}}" />
              <input type="hidden" name="sejour_id" value="" />
              <input type="hidden" name="date" value="{{$date}}" />

              <table class="main form">
                <tr>
                  <th></th>
                  <td>
                    <select name="mode" onchange="this.form.submit()" style="width:135px">
                      <option value="0" {{if $mode == 0}}selected{{/if}}>{{tr}}Instant view{{/tr}}</option>
                      <option value="1" {{if $mode == 1}}selected{{/if}}>{{tr}}Day view{{/tr}}</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <th>
                    <label for="service_id">
                      <button type="button" class="search notext" title="Service par défaut" onclick="Modal.open('select_default_service', { showClose: true, title: 'Service par défaut' })"></button>
                      Service
                    </label>
                  </th>
                  <td>
                    <select name="service_id" onchange="checkAnesth(this); this.form.submit()" style="max-width: 135px;">
                      <option value="">&mdash; Service</option>
                      {{foreach from=$services item=curr_service}}
                      <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service_id}}selected{{/if}}>{{$curr_service->nom}}</option>
                      {{/foreach}}
                      <option value="NP" {{if $service_id == "NP"}}selected{{/if}}>Non placés</option>
                    </select>
                    {{if "dPprescription"|module_active}}
                      <button type="button" class="search compact" onclick="viewBilanService('{{$service_id}}','{{$date}}');">Bilan</button>
                    {{/if}}

                    <div id="select_default_service" style="display: none;">
                      <table class="form">
                        <tr>
                          <td style="text-align: center;">
                            <select name="default_service_id">
                              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                              {{foreach from=$services item=_service}}
                                <option value="{{$_service->_id}}" {{if $default_service_id == $_service->_id}}selected{{/if}}>{{$_service->_view}}</option>
                              {{/foreach}}
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td class="button">
                            <button type="button" class="submit" onclick="savePref(this.form);">{{tr}}Save{{/tr}}</button>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>

                <tr>
                  <th><label for="praticien_id">Praticien</label></th>
                  <td>
                    <select name="praticien_id" onchange="checkAnesth(this); this.form.submit();" style="width: 135px;">
                      <option value="">&mdash; Choix du praticien</option>
                      {{mb_include module=mediusers template=inc_options_mediuser selected=$praticien_id list=$praticiens}}
                    </select>
                  </td>
                </tr>

                <tr>
                  <th>{{mb_title class=CSejour field="type"}}</th>
                  <td>
                    {{assign var=type_admission value=$object->_specs.type}} 
                    <select name="type" onchange="this.form.submit();" style="width: 135px;">
                      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                      {{foreach from=$type_admission->_locales key=key item=_type}} 
                      {{if $key != "urg" && $key != "exte"}}
                      <option value="{{$key}}" {{if $key == $object->type}}selected{{/if}}>{{$_type}}</option>
                      {{/if}}
                      {{/foreach}}
                    </select>
                  </td>
                </tr>

                {{if $app->_ref_user->isInfirmiere() || $app->_ref_user->isAideSoignant() || $app->_ref_user->isSageFemme()}}
                  <tr>
                    <th>Mes patients ({{$count_my_patient}})</th>
                    <td>
                      <input type="hidden" name="my_patient" value="{{$my_patient}}" onchange="this.form.submit();"/>
                      <input type="checkbox" name="change_patient" value="{{if $my_patient == 1}}0{{else}}1{{/if}}" {{if $my_patient == 1}}checked{{/if}} onchange="$V(this.form.my_patient, this.checked?1:0);"/>
                    </td>
                  </tr>
                {{/if}}
              </table>
            </form>

            <form name="editPrefServiceSoins" method="post">
              <input type="hidden" name="m" value="admin" />
              <input type="hidden" name="dosql" value="do_preference_aed" />
              <input type="hidden" name="user_id" value="{{$app->user_id}}" />
              <input type="hidden" name="pref[default_services_id]" value="{{$app->user_prefs.default_services_id}}" />
            </form>
          </td>
        </tr>
        
        {{if $_is_praticien && ($dnow == $date)}}
          <tr>
            <td class="button">
              <script>
                function createNotifications(){
                  var sejours = {{$visites.non_effectuee|@json}};
                  var url = new Url("soins", "httpreq_notifications_visite");
                  url.addParam("sejours[]", sejours);
                  url.requestUpdate("systemMsg", { onComplete: function() {
                    $("tooltip-visite-{{$app->user_id}}-{{$date}}").update(DOM.div( {className: 'small-info'}, "Visites validées"));
                  } } );
                }
              </script>
              
            <a href="#Create-Notifications" class="button search" onmouseover="ObjectTooltip.createDOM(this, 'tooltip-visite-{{$app->user_id}}-{{$date}}')";>
              Mes visites
            </a>
            
            <table class="form" id="tooltip-visite-{{$app->user_id}}-{{$date}}" style="display: none;">
              {{if $visites.effectuee|@count}}
                <tr>
                  <th>Visites effectuée(s)</th>
                  <td>{{$visites.effectuee|@count}}</td>
                </tr>
              {{/if}}
              
              {{if $visites.non_effectuee|@count}}
                <tr>
                  <th>Visites à effectuer</th>
                  <td>{{$visites.non_effectuee|@count}}</td>
                </tr>

                <tr>
                  <td colspan="2" class="button">
                    <button type="button tick" class="tick" onclick="createNotifications();">
                      Valider les visites
                    </button>
                  </td>
                </tr>
              {{/if}} 
              
              {{if !$visites.effectuee|@count && !$visites.non_effectuee|@count}}
                <tr>
                  <td colspan="2" class="empty">Aucune visite dans la sélection courante</td>
                </tr>
              {{/if}}
            </table>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td style="padding: 0;">
            <div style="overflow: auto; height: 500px; position: relative;" class="scroller">
            <table class="tbl" id="list_sejours">
            {{foreach from=$sejoursParService key=_service_id item=service}}
              {{if array_key_exists($_service_id, $services)}}
              <tr>
                {{assign var=_service value=$services.$_service_id}}
                <th colspan="6" class="title">{{$_service->_view}}</th>
              </tr>
              {{foreach from=$service->_ref_chambres item=curr_chambre}}
              {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
              <tr>
                <th class="category {{if !$curr_lit->_ref_affectations|@count}}opacity-50{{/if}}" colspan="6" style="font-size: 0.9em;">
                  {{if "soins CLit align_right"|conf:"CGroups-$g"}}
                  <span style="float: left;">{{$curr_chambre}}</span>
                  <span style="float: right;">{{$curr_lit->_shortview}}</span>
                  {{else}}
                  <span style="float: left;">{{$curr_chambre}} - {{$curr_lit->_shortview}}</span>
                  {{/if}}
                </th>
              </tr>
              {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
              {{if $curr_affectation->_ref_sejour->_id != ""}}
                {{assign var=sejour value=$curr_affectation->_ref_sejour}}
              <tr class="{{if $object->_id == $curr_affectation->_ref_sejour->_id}}selected{{/if}} {{$sejour->type}}">
                <td style="padding: 0;">
                  <button class="lookup notext" style="margin:0;" onclick="popEtatSejour({{$sejour->_id}});">
                    {{tr}}Lookup{{/tr}}
                  </button>
                  {{if @$modules.dPplanningOp->_can->admin}}
                    <button class="mediuser_black notext" onclick="paramUserSejour({{$curr_affectation->sejour_id}});"
                            style="margin-right: 5px;{{if !$sejour->_ref_users_sejour|@count}}opacity: 0.6;{{/if}}"
                            onmouseover="ObjectTooltip.createDOM(this, 'affectation_CSejour-{{$sejour->_id}}')";></button>
                    {{if $sejour->_ref_users_sejour|@count}}
                      <span class="countertip" style="margin-top:1px;margin-left: -10px;">
                        <span>{{$sejour->_ref_users_sejour|@count}}</span>
                      </span>
                    {{/if}}
                    {{mb_include module=planningOp template=vw_user_sejour_table}}
                  {{/if}}
                </td>

                <td class="text">
                  {{assign var=aff_next value=$curr_affectation->_ref_next}}
                  {{assign var=sejour value=$curr_affectation->_ref_sejour}}

                  <a class="text" href="#1"
                     onclick="loadViewSejour('{{$sejour->_id}}',  '{{$date}}', this);">
                    <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
                      {{$sejour->_ref_patient->_view}}
                    </span>
                  </a>
                </td>

                {{if "soins dossier_soins show_ampoule_patient"|conf:"CGroups-$g"}}
                  <td>
                    {{if $sejour->_ref_prescriptions && array_key_exists('sejour', $sejour->_ref_prescriptions)}}
                      {{assign var=prescription value=$sejour->_ref_prescriptions.sejour}}
                      {{assign var=prescription_guid value=$prescription->_guid}}
                      {{if $prescription->_id}}
                        {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
                          {{mb_script module=system script=alert}}
                          <span id="span-icon-alert-medium-{{$prescription_guid}}">
                            {{mb_include module=system template=inc_icon_alerts object=$prescription nb_alerts=$prescription->_count_alertes
                            callback="function() { compteurAlerte('medium', '$prescription_guid')}"}}
                          </span>
                          <span id="span-icon-alert-high-{{$prescription_guid}}">
                            {{mb_include module=system template=inc_icon_alerts object=$prescription level="high" nb_alerts=$prescription->_count_urgences
                            callback="function() { compteurAlerte('high', '$prescription_guid')}"}}
                          </span>
                        {{elseif $prescription->_count_fast_recent_modif}}
                          <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
                          {{mb_include module=system template=inc_vw_counter_tip count=$prescription->_count_fast_recent_modif top="-5px" right="-15px"}}
                        {{/if}}
                      {{/if}}
                    {{/if}}
                  </td>
                {{/if}}
                <td style="padding: 1px;" >
                  {{if $isImedsInstalled}}
                    <div class="Imeds_button" onclick="loadViewSejour('{{$sejour->_id}}', '{{$date}}', this, 'Imeds');">
                      {{mb_include module=Imeds template=inc_sejour_labo link="#"}}
                    </div>
                  {{/if}}
                  {{mb_include module=dPfiles template=inc_icon_category_check object=$sejour}}
                </td>

                <td class="action" style="padding: 1px;">
                  <span>
                    {{if $sejour->type == "ambu"}}
                      <img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" alt="X" title="Ambulatoire" />
                    {{elseif $curr_affectation->sortie|iso_date == $demain}}
                      {{if $aff_next->_id}}
                        <img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" alt="OC" title="Déplacé demain" />
                      {{else}}
                        <img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" alt="O" title="Sortant demain" />
                      {{/if}}
                    {{elseif $curr_affectation->sortie|iso_date == $date}}
                      {{if $aff_next->_id}}
                        <img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" alt="OoC" title="Déplacé aujourd'hui" />
                      {{else}}
                        <img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" alt="Oo" title="Sortant aujourd'hui" />
                      {{/if}}
                    {{/if}}
                  </span>
                  <div class="mediuser" style="border-color:#{{$sejour->_ref_praticien->_ref_function->color}}; display: inline;">
                    <label title="{{$sejour->_ref_praticien->_view}}">
                    {{$sejour->_ref_praticien->_shortview}}
                    </label>
                  </div>
                </td>
              </tr>
            {{/if}}
            {{/foreach}}
            {{/foreach}}
            {{/foreach}}
            {{if $service->_ref_affectations_couloir && $service->_ref_affectations_couloir|@count != 0}}
              <tr>
                <th class="category" colspan="6">
                  Couloir
                </th>
              </tr>
              {{foreach from=$service->_ref_affectations_couloir item=curr_affectation}}
                {{if $curr_affectation->_ref_sejour->_id != ""}}
                {{assign var=sejour value=$curr_affectation->_ref_sejour}}
              <tr class="{{if $object->_id == $curr_affectation->_ref_sejour->_id}}selected{{/if}} {{$sejour->type}}">
                <td style="padding: 0;">
                  <button class="lookup notext" style="margin:0;" onclick="popEtatSejour({{$sejour->_id}});">
                    {{tr}}Lookup{{/tr}}
                  </button>
                  {{if @$modules.dPplanningOp->_can->admin}}
                    <button class="mediuser_black notext" onclick="paramUserSejour({{$curr_affectation->sejour_id}});" style="margin-right: 5px;"
                            onmouseover="ObjectTooltip.createDOM(this, 'affectation_CSejour-{{$sejour->_id}}')";></button>
                    <span class="countertip" style="margin-top:1px;margin-left: -10px;">
                      <span class="{{if !$sejour->_ref_users_sejour|@count}}empty{{/if}}">{{$sejour->_ref_users_sejour|@count}}</span>
                    </span>
                    <div style="display: none" id="affectation_CSejour-{{$curr_affectation->sejour_id}}">
                      <table class="tbl">
                        {{foreach from=$sejour->_ref_users_by_type item=_users key=type}}
                          <tr>
                            <th>{{tr}}CUserSejour.{{$type}}{{/tr}}</th>
                          </tr>
                          {{foreach from=$_users item=_user}}
                            <tr>
                              <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_user->_ref_user}}</td>
                            </tr>
                          {{foreachelse}}
                            <tr>
                              <td class="empty">{{tr}}CUserSejour.none{{/tr}}</td>
                            </tr>
                          {{/foreach}}
                        {{/foreach}}
                      </table>
                    </div>
                  {{/if}}
                </td>

                <td class="text">
                  {{assign var=aff_next value=$curr_affectation->_ref_next}}
                  {{assign var=sejour value=$curr_affectation->_ref_sejour}}

                  <a class="text" href="#1"
                     onclick="loadViewSejour('{{$sejour->_id}}',  '{{$date}}', this);">
                    <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
                      {{$sejour->_ref_patient->_view}}
                    </span>
                  </a>
                </td>

                {{if "soins dossier_soins show_ampoule_patient"|conf:"CGroups-$g"}}
                  <td>
                    {{if $sejour->_ref_prescriptions && array_key_exists('sejour', $sejour->_ref_prescriptions)}}
                      {{assign var=prescription value=$sejour->_ref_prescriptions.sejour}}
                      {{assign var=prescription_guid value=$prescription->_guid}}
                      {{if $prescription->_id}}
                        {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
                          {{mb_script module=system script=alert}}
                          <span id="span-icon-alert-medium-{{$prescription_guid}}">
                            {{mb_include module=system template=inc_icon_alerts object=$prescription nb_alerts=$prescription->_count_alertes
                            callback="function() { compteurAlerte('medium', '$prescription_guid')}"}}
                          </span>
                          <span id="span-icon-alert-high-{{$prescription_guid}}">
                            {{mb_include module=system template=inc_icon_alerts object=$prescription level="high" nb_alerts=$prescription->_count_urgences
                            callback="function() { compteurAlerte('high', '$prescription_guid')}"}}
                          </span>
                        {{elseif $prescription->_count_fast_recent_modif}}
                          <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
                          {{mb_include module=system template=inc_vw_counter_tip count=$prescription->_count_fast_recent_modif top="-5px" right="-15px"}}
                        {{/if}}
                      {{/if}}
                    {{/if}}
                  </td>
                {{/if}}
                <td style="padding: 1px;" >
                  {{if $isImedsInstalled}}
                    <div class="Imeds_button" onclick="loadViewSejour('{{$sejour->_id}}', '{{$date}}', this, 'Imeds');">
                      {{mb_include module=Imeds template=inc_sejour_labo link="#"}}
                    </div>
                  {{/if}}
                  {{mb_include module=dPfiles template=inc_icon_category_check object=$sejour}}
                </td>

                <td class="action" style="padding: 1px;">
                  <span>
                    {{if $sejour->type == "ambu"}}
                      <img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" alt="X" title="Ambulatoire" />
                    {{elseif $curr_affectation->sortie|iso_date == $demain}}
                      {{if $aff_next->_id}}
                        <img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" alt="OC" title="Déplacé demain" />
                      {{else}}
                        <img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" alt="O" title="Sortant demain" />
                      {{/if}}
                    {{elseif $curr_affectation->sortie|iso_date == $date}}
                      {{if $aff_next->_id}}
                        <img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" alt="OoC" title="Déplacé aujourd'hui" />
                      {{else}}
                        <img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" alt="Oo" title="Sortant aujourd'hui" />
                      {{/if}}
                    {{/if}}
                  </span>
                  <div class="mediuser" style="border-color:#{{$sejour->_ref_praticien->_ref_function->color}}; display: inline;">
                    <label title="{{$sejour->_ref_praticien->_view}}">
                    {{$sejour->_ref_praticien->_shortview}}
                    </label>
                  </div>
                </td>
              </tr>
            {{/if}}
            {{/foreach}}
            {{/if}}
            {{/if}}
           {{/foreach}}

            <!-- Cas de l'affichage par praticien -->
            {{if $praticien_id}}
              {{if array_key_exists('NP', $sejoursParService)}}
                <tr>
                  <th class="title" colspan="6">Non placés</th>
                </tr>
                {{foreach from=$sejoursParService.NP item=_sejour_NP}}
                  {{mb_include module="hospi" template="inc_vw_sejour_np" curr_sejour=$_sejour_NP}}
                {{/foreach}}
              {{/if}}
            {{/if}}
            
            <!-- Cas de l'affichage par service -->
            {{if $service_id}}
              {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
                <tr>
                  <th class="title" colspan="6">
                    {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
                  </th>
                </tr>
                {{foreach from=$sejourNonAffectes item=curr_sejour}}
                  {{mb_include module="hospi" template="inc_vw_sejour_np"}}
                {{/foreach}}
              {{/foreach}}
            {{/if}}
            </table>
            </div>
          </td>
        </tr> 
      </table>    
    </td>
    <td style="width: 100%;">
      <div id="dossier_sejour">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour afficher
          le dossier de soin du patient concerné.
        </div>
      </div>
    </td>
  </tr>
</table>