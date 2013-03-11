{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{if $isImedsInstalled}}
{{mb_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{mb_script module="soins" script="plan_soins"}}
  
{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="element_selector"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

{{mb_script module="dPpatients" script="patient"}}
{{assign var="do_subject_aed" value="do_sejour_aed"}}
{{assign var="module" value="dPhospi"}}
{{mb_include module=salleOp template=js_codage_ccam}}
{{if $conf.dPhospi.CLit.alt_icons_sortants}}
  {{assign var=suffixe_icons value="2"}}
{{else}}
  {{assign var=suffixe_icons value=""}}
{{/if}}

<script type="text/javascript">
     
function loadActesNGAP(sejour_id){
  var url = new Url("dPcabinet", "httpreq_vw_actes_ngap");
  url.addParam("object_id", sejour_id);
  url.addParam("object_class", "CSejour");
  url.requestUpdate('listActesNGAP');
}

function loadSuiviClinique(sejour_id) {
  var url = new Url("soins", "ajax_vw_suivi_clinique");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("suivi_clinique");
}

function loadDocuments(sejour_id) {
  var url = new Url("dPhospi", "httpreq_documents_sejour");
  url.addParam("sejour_id" , sejour_id);
  url.requestUpdate("documents");
}


function popEtatSejour(sejour_id) {
  var url = new Url("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 650, 'Etat du Séjour');
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim");
}

{{if $isPrescriptionInstalled}}
  function reloadPrescription(prescription_id){
    Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null,'',null, false, '0');
  }
{{/if}}


function addSejourIdToSession(sejour_id){
  var url = new Url("system", "httpreq_set_value_to_session");
  url.addParam("module","{{$m}}");
  url.addParam("name","sejour_id");
  url.addParam("value",sejour_id);
  url.requestUpdate("systemMsg");
}

// Cet objet doit contenir des entrée clé/valeur dont la clé est l'ID du 
// conteneur du volet et en valeur la fonction appelée pour charger ce volet.
window.tabLoaders = {
  "suivi_clinique": function(sejour_id, praticien_id, date){
    if(!$("suivi_clinique").visible()) return;
    
    loadSuiviClinique(sejour_id);
  },
  "constantes-medicales": function(sejour_id, praticien_id, date){
    if(!$("constantes-medicales").visible()) return;
    
    refreshConstantesMedicales("CSejour-"+sejour_id, 1);
  },
  
    "dossier_traitement": function(sejour_id, praticien_id, date){
      if(!$("dossier_traitement").visible()) return;
      
      PlanSoins.loadTraitement(sejour_id, date,'','administration');
    },
    
  {{if "dPprescription"|module_active}}
    "prescription_sejour": function(sejour_id, praticien_id, date){
      if(!$("prescription_sejour").visible()) return;
      
      Prescription.reloadPrescSejour('', sejour_id, null, null, null, null, null, null, null, false, '0');
    },
  {{/if}}
       
  {{if $app->user_prefs.ccam_sejour == 1 }}
    "Actes": function(sejour_id, praticien_id, date){
      if($('listActesNGAP')){
        loadActesNGAP(sejour_id);
      }
      if($('ccam')){
        ActesCCAM.refreshList(sejour_id, praticien_id);
      }
      if($('cim')){
        reloadDiagnostic(sejour_id, '1');
      }
    },
  {{/if}}
  
  {{if $isImedsInstalled}}
    "Imeds": function(sejour_id, praticien_id, date){
      loadResultLabo(sejour_id);
    },
  {{/if}}
  
  "documents": function(sejour_id, praticien_id, date){
    loadDocuments(sejour_id);
  },
  
  {{if $can_view_dossier_medical}}
    "antecedents": function(sejour_id, praticien_id, date){
      loadAntecedents(sejour_id);
    },
  {{/if}} 
};

function loadViewSejour(sejour_id, praticien_id, patient_id, date){
  document.form_prescription.sejour_id.value = sejour_id;
  
  var loaders = $H(window.tabLoaders);
  var activeTab = window.tab_sejour.activeContainer.id;
  
  // Chargement du volet actif en premier
  window.tabLoaders[activeTab](sejour_id, praticien_id, date);
  
  // Chargement des autres
  loaders.each(function(pair){
    if (pair.key != activeTab && $(pair.key)) {
      pair.value(sejour_id, praticien_id, date);
    }
  });
}

{{if $can_view_dossier_medical}}
function loadAntecedents(sejour_id){
  var url = new Url("dPcabinet","httpreq_vw_antecedents");
  url.addParam("sejour_id", sejour_id);
  url.addParam("show_header", 1);
  url.requestUpdate('antecedents')
}
{{/if}}

function loadResultLabo(sejour_id) {
  var url = new Url("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('Imeds');
}

// Cette fonction est dupliquée
function updateNbTrans(sejour_id) {
  var url = new Url("hospi", "ajax_count_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.requestJSON(function(count)  {
    Control.Tabs.setTabCount('dossier_suivi', count);
  });
}

function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
  if(!sejour_id) return;

  updateNbTrans(sejour_id);
  var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
  urlSuivi.addParam("sejour_id", sejour_id);
  urlSuivi.addParam("user_id", user_id);
  if (!Object.isUndefined(cible)) {
    urlSuivi.addParam("cible", cible);
  }
  if (!Object.isUndefined(show_obs)) {
    urlSuivi.addParam("_show_obs", show_obs);
  }
  if (!Object.isUndefined(show_trans)) {
    urlSuivi.addParam("_show_trans", show_trans);
  }
  if (!Object.isUndefined(show_const)) {
    urlSuivi.addParam("_show_const", show_const);
  }
  urlSuivi.requestUpdate("dossier_suivi");
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() {
    if($V(oForm.object_class)|| $V(oForm.libelle_ATC)){
      // Refresh de la partie administration
      if($('jour').visible()){
        PlanSoins.loadTraitement(sejour_id,'{{$date}}','','administration');
      }
      // Refresh de la partie plan de soin
      if($('semaine').visible()){
        {{if $object->_ref_prescriptions}}
        calculSoinSemaine('{{$date}}', '{{$object->_ref_prescriptions.sejour->_id}}');
        {{/if}}
      }
    }
    if ($('dossier_suivi').visible()) {
      loadSuivi(sejour_id);
    }
    updateNbTrans(sejour_id);
  } });
}

function refreshConstantesMedicales(context_guid, paginate, count) {
  if(context_guid && context_guid.split("-")[1]) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    url.addParam("paginate", paginate || 0);
    if (count) {
      url.addParam("count", count);
    }
    url.requestUpdate("constantes-medicales");
  }
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

var tab_sejour = null;

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});

  // Tab initialization
  window.tab_sejour = Control.Tabs.create('tab-sejour', true);
  
  // Activation d'un onglet
  {{if $_active_tab}}
    tab_sejour.setActiveTab('{{$_active_tab}}');
  {{/if}}
  
  {{if $app->user_prefs.ccam_sejour == 1 }}
  var tab_actes = Control.Tabs.create('tab-actes', false);
  {{/if}}

  {{if $object->_id}}
  loadViewSejour('{{$object->_id}}', null, '{{$object->patient_id}}', '{{$date}}');
  {{/if}}
  
  {{if $isImedsInstalled}}
    ImedsResultsWatcher.loadResults();
  {{/if}}
  
  updatePatientsListHeight();
  
  Event.observe(window, "resize", updatePatientsListHeight);
});

function markAsSelected(element) {
  element.up("tr").addUniqueClassName("selected");
}

viewBilanService = function(service_id, date){
  var url = new Url("dPhospi", "vw_bilan_service");
  url.addParam("service_id", service_id);
  url.addParam("date", date);
  url.popup(800,500,"Bilan par service");
}

printDossierComplet = function(){
  var url = new Url("soins", "print_dossier_soins");
  url.addParam("sejour_id", $V(document.form_prescription.sejour_id));
  url.popup(850, 600, "Dossier complet");
}

checkAnesth = function(oField){
  // Recuperation de la liste des anesthésistes
  var anesthesistes = {{$anesthesistes|@json}};
  
  var oForm = getForm("selService");
  var praticien_id = $V(oForm.praticien_id);
  var service_id   = $V(oForm.service_id);
  
  if (oField.name == "service_id"){
    if(anesthesistes.include(praticien_id)){
      $V(oForm.praticien_id, '', false);
    }
  }
  
  if (oField.name == "praticien_id"){
    if(anesthesistes.include(praticien_id)){
      $V(oForm.service_id, '', false);    
    }
  }
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
             
              <table class="main form">
                <tr>
                  <th></th>
                  <td>
                    <select name="mode" onchange="this.form.submit()" style="width:135px">
                      <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>{{tr}}Instant view{{/tr}}</option>
                      <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>{{tr}}Day view{{/tr}}</option>
                    </select>
                  </td>
                </tr>
                
                <tr>
                  <th><label for="service_id">Service</label></th>
                  <td>
                    <select name="service_id" onchange="checkAnesth(this); this.form.submit()" style="max-width: 135px;">
                      <option value="">&mdash; Service</option>
                      {{foreach from=$services item=curr_service}}
                      <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service_id}} selected="selected" {{/if}}>{{$curr_service->nom}}</option>
                      {{/foreach}}
                      <option value="NP" {{if $service_id == "NP"}} selected="selected" {{/if}}>Non placés</option>
                    </select>
                    {{if $service_id && $isPrescriptionInstalled && $service_id != "NP"}}
                      <button type="button" class="search compact" onclick="viewBilanService('{{$service_id}}','{{$date}}');">Bilan</button>
                    {{/if}}
                  </td>
                </tr>
                
                <tr>
                  <th><label for="praticien_id">Praticien</label></th>
                  <td>
                    <select name="praticien_id" onchange="checkAnesth(this); this.form.submit();" style="width: 135px;">
                      <option value="">&mdash; Choix du praticien</option>
                      {{foreach from=$praticiens item=_prat}}
                        <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}" {{if $_prat->_id == $praticien_id}}selected="selected"{{/if}}>
                          {{$_prat->_view}}
                        </option>
                      {{/foreach}}
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
                      <option value="{{$key}}" {{if $key == $object->type}}selected="selected"{{/if}}>{{$_type}}</option>
                      {{/if}}
                      {{/foreach}}
                    </select>
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
        
        {{if $_is_praticien && ($current_date == $date)}}
          <tr>
            <td class="button">
              <script type="text/javascript">
              function createNotifications(){
                var sejours = {{$visites.non_effectuee|@json}};
                var url = new Url("soins", "httpreq_notifications_visite");
                url.addParam("sejours[]", sejours);
                url.requestUpdate("systemMsg", { onComplete: function() { 
                  $("tooltip-visite-{{$app->user_id}}-{{$date}}").update(DOM.div( {className: 'small-info'}, "Visites validées"));
                } } );
              }
              </script>
              
            <a href="#Create-Notifications" class="button search" onmouseover='ObjectTooltip.createDOM(this, "tooltip-visite-{{$app->user_id}}-{{$date}}")'>
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
            <div style="{{if $smarty.session.browser.name == 'msie' && $smarty.session.browser.majorver < 8}}overflow:visible; overflow-x:hidden; overflow-y:auto; padding-right:15px;{{else}}overflow: auto;{{/if}} height: 500px;" class="scroller">
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
                  {{if $conf.soins.CLit.align_right}}
                  <span style="float: left;">{{$curr_chambre}}</span>
                  <span style="float: right;">{{$curr_lit->_shortview}}</span>
                  {{else}}
                  <span style="float: left;">{{$curr_chambre}} - {{$curr_lit->_shortview}}</span>
                  {{/if}}
                </th>
              </tr> 
              {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
              {{if $curr_affectation->_ref_sejour->_id != ""}}
              <tr class="{{if $object->_id == $curr_affectation->_ref_sejour->_id}}selected{{/if}} {{$curr_affectation->_ref_sejour->type}}">
                <td style="padding: 0;">
                  <button class="lookup notext" style="margin:0;" onclick="popEtatSejour({{$curr_affectation->_ref_sejour->_id}});">
                    {{tr}}Lookup{{/tr}}
                  </button>
                </td>
                
                <td class="text">
                  {{assign var=aff_next value=$curr_affectation->_ref_next}}
                  {{assign var=sejour value=$curr_affectation->_ref_sejour}}
                  {{assign var=prescriptions value=$sejour->_ref_prescriptions}}
                  {{assign var=prescription_sejour value=$prescriptions.sejour}}
                  {{assign var=prescription_sortie value=$prescriptions.sortie}}

                  <a class="text" href="#1" 
                     onclick="markAsSelected(this); addSejourIdToSession('{{$sejour->_id}}'); loadViewSejour('{{$sejour->_id}}', {{$sejour->praticien_id}}, {{$sejour->patient_id}}, '{{$date}}');">
                    <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}">
                      {{$sejour->_ref_patient->_view}}
                    </span>
                  </a>
                </td>

                <td style="padding: 1px;" onclick="markAsSelected(this); addSejourIdToSession('{{$sejour->_id}}'); loadViewSejour('{{$sejour->_id}}', {{$sejour->praticien_id}}, {{$sejour->patient_id}}, '{{$date}}'); tab_sejour.setActiveTab('Imeds')">
                  {{if $isImedsInstalled}}
                    {{mb_include module=Imeds template=inc_sejour_labo link="#"}}
                  {{/if}}
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
                
                {{if $isPrescriptionInstalled}}
                <td style="padding: 1px;">
                  {{if $prescription_sejour->_id && (!$prescription_sortie->_id || $prescription_sejour->_counts_no_valide)}}
                    <img src="images/icons/warning.png" width="12"
                       onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-alertes-{{$sejour->_guid}}')" />
                  {{/if}}
                  
                  <div id="tooltip-content-alertes-{{$sejour->_guid}}" style="display: none;">
                    <ul>
                     {{if !$prescription_sortie->_id}}
                       <li>Ce séjour ne possède pas de prescription de sortie</li>
                     {{/if}}
                     {{if $prescription_sejour->_counts_no_valide}}
                       <li>Lignes non validées dans la prescription de séjour</li>
                     {{/if}}
                    </ul>
                  </div>
                </td>
                {{/if}}
              </tr>
            {{/if}}
            {{/foreach}}
            {{/foreach}}
            {{/foreach}}
            {{/if}}
           {{/foreach}}

            <!-- Cas de l'affichage par praticien -->
            {{if $praticien_id}}
              {{if array_key_exists('NP', $sejoursParService)}}
                <tr>
                  <th class="title" colspan="6">Non placés</th>
                </tr>
                {{foreach from=$sejoursParService.NP item=_sejour_NP}}
                  {{include file="../../dPhospi/templates/inc_vw_sejour_np.tpl" curr_sejour=$_sejour_NP}}
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
                  {{include file="../../dPhospi/templates/inc_vw_sejour_np.tpl"}}
                {{/foreach}}
              {{/foreach}}
            {{/if}}
            </table>
            </div>
          </td>
        </tr> 
      </table>    
    </td>
    <td style="width:100%;">
      <!-- Tab titles -->
      <ul id="tab-sejour" class="control_tabs">
        <li>
          <button type="button" class="hslip notext compact" style="vertical-align: bottom;" onclick="$('left-column').toggle();" title="Afficher/cacher la colonne de gauche"></button>
        </li>
        <li><a href="#suivi_clinique" onmousedown="loadSuiviClinique(document.form_prescription.sejour_id.value)">{{tr}}CSejour.suivi_clinique{{/tr}}</a></li>
        <li onmousedown="refreshConstantesMedicales('CSejour-'+document.form_prescription.sejour_id.value, 1)"><a href="#constantes-medicales">{{tr}}CPatient.surveillance{{/tr}}</a></li>
        <li onmousedown="PlanSoins.loadTraitement(document.form_prescription.sejour_id.value,'{{$date}}','','administration')"><a href="#dossier_traitement">{{tr}}CSejour.suivi_soins{{/tr}}</a></li>
        {{if "dPprescription"|module_active}}
        <li onmousedown="$('prescription_sejour').update(''); Prescription.reloadPrescSejour('', document.form_prescription.sejour_id.value, null, null, null, null, null, '', null, false);">
          <a href="#prescription_sejour">Prescription</a>
        </li>
        {{/if}}
        {{if $app->user_prefs.ccam_sejour == 1 }}
          <li><a href="#Actes">Cotation</a></li>
        {{/if}}
        {{if $isImedsInstalled}}
          <li><a href="#Imeds">Labo</a></li>
        {{/if}}
        <li><a href="#documents">Documents</a></li>
        {{if $can_view_dossier_medical}}
        <li onmousedown="DossierMedical.reloadDossierSejour();"><a href="#antecedents">Antécédents</a></li>
        {{/if}} 
        <li style="float: right">
          <button type="button" class="button print" onclick="printDossierComplet();">Dossier soins</button>
        </li>
      </ul>
      
      <hr class="control_tabs" />
      
      <!-- Tabs -->
      <div id="suivi_clinique" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour afficher
          ici toutes les informations sur le patient et le séjour.
        </div>
      </div>
      
      <div id="constantes-medicales" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour afficher
          les constantes du patient concerné.
        </div>
      </div>
      
      <div id="dossier_traitement" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour afficher
          le dossier de soin du patient concerné.
        </div>
      </div>
      
      {{if "dPprescription"|module_active}}
      <div id="prescription_sejour" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour afficher
          la prescription du patient concerné.
        </div>
      </div>
      {{/if}}
      
      {{if $app->user_prefs.ccam_sejour == 1 }}
      <div id="Actes" style="display: none;">
        <ul id="tab-actes" class="control_tabs">
          <li><a href="#one">Actes CCAM</a></li>
          <li><a href="#two">Actes NGAP</a></li>
          <li><a href="#three">Diagnostics</a></li>
        </ul>
        <hr class="control_tabs" />
        
        <table class="form">
          <tr id="one" style="display: none;">
            <td id="ccam">
              <div class="small-info">
                Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
                ajouter des actes CCAM au patient concerné.
              </div>
            </td>
          </tr>
          <tr id="two" style="display: none;">
            <td id="listActesNGAP">
              <div class="small-info">
                Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
                ajouter des actes NGAP au patient concerné.
              </div>
            </td>
          </tr>
          <tr id="three" style="display: none;">
            <td id="cim">
              <div class="small-info">
                Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
                ajouter des actes diagnostics CIM au patient concerné.
              </div>
            </td>
          </tr>
        </table>
      </div>
      {{/if}}
    
      {{if $isImedsInstalled}}
      <div id="Imeds" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
          consulter les résultats de laboratoire disponibles pour le patient concerné.
        </div>
      </div>
      {{/if}}
      
      <div id="documents" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
          consulter et ajouter des documents pour le patient concerné.
        </div>
      </div>

      {{if $can_view_dossier_medical}}
      <div id="antecedents" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
          consulter et modifier les antécédents du patient concerné.
        </div>
      </div>
      {{/if}}
    </td>
  </tr>
</table>