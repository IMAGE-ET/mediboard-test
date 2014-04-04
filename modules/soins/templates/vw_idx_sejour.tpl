{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{if $conf.dPhospi.CLit.alt_icons_sortants}}
  {{assign var=suffixe_icons value="2"}}
{{else}}
  {{assign var=suffixe_icons value=""}}
{{/if}}

<script>


function popEtatSejour(sejour_id) {
  var url = new Url("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 650, 'Etat du Séjour');
}

function addSejourIdToSession(sejour_id){
  var url = new Url("system", "httpreq_set_value_to_session");
  url.addParam("module","{{$m}}");
  url.addParam("name","sejour_id");
  url.addParam("value",sejour_id);
  url.requestUpdate("systemMsg");
}

function loadViewSejour(sejour_id, date){
  var url = new Url('soins', 'ajax_vw_dossier_sejour');
  url.addParam('sejour_id', sejour_id);
  url.addParam('date', date);
  url.requestUpdate('dossier_sejour');
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

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});

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

savePref = function(form) {
  var formPref = getForm('editPrefServiceSoins');
  var formService = getForm('selService');
  var service_id = $V(form.default_service_id);

  var services_ids_hospi_elt = formPref.elements['pref[services_ids_hospi]'];
  var services_ids_hospi = $V(services_ids_hospi_elt).evalJSON();
  services_ids_hospi.g{{$group_id}} = service_id;
  $V(services_ids_hospi_elt, Object.toJSON(services_ids_hospi));
  return onSubmitFormAjax(formPref, {onComplete: function() {
    Control.Modal.close();
    $V(formService.service_id, service_id);
  } });
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
                      <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service_id}} selected="selected" {{/if}}>{{$curr_service->nom}}</option>
                      {{/foreach}}
                      <option value="NP" {{if $service_id == "NP"}} selected="selected" {{/if}}>Non placés</option>
                    </select>
                    {{if $service_id && $isPrescriptionInstalled && $service_id != "NP"}}
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
                      <option value="{{$key}}" {{if $key == $object->type}}selected="selected"{{/if}}>{{$_type}}</option>
                      {{/if}}
                      {{/foreach}}
                    </select>
                  </td>
                </tr>
              </table>
            </form>

            <form name="editPrefServiceSoins" method="post">
              <input type="hidden" name="m" value="admin" />
              <input type="hidden" name="dosql" value="do_preference_aed" />
              <input type="hidden" name="user_id" value="{{$app->user_id}}" />
              <input type="hidden" name="pref[services_ids_hospi]" value="{{$services_ids_hospi}}" />
            </form>
          </td>
        </tr>
        
        {{if $_is_praticien && ($current_date == $date)}}
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

                  <a class="text" href="#1" 
                     onclick="markAsSelected(this); addSejourIdToSession('{{$sejour->_id}}'); loadViewSejour('{{$sejour->_id}}',  '{{$date}}');">
                    <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
                      {{$sejour->_ref_patient->_view}}
                    </span>
                  </a>
                </td>

                <td style="padding: 1px;" onclick="markAsSelected(this); addSejourIdToSession('{{$sejour->_id}}'); loadViewSejour('{{$sejour->_id}}', '{{$date}}'); tab_sejour.setActiveTab('Imeds')">
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
      <div id="dossier_sejour">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour afficher
          le dossier de soin du patient concerné.
        </div>
      </div>
    </td>
  </tr>
</table>