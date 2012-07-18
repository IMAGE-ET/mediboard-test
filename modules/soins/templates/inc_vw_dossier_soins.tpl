{{* $Id: inc_vw_dossier_soins.tpl 14528 2012-02-02 14:52:03Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 14528 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $sejour->_id}}

<script type="text/javascript">
  
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
  
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, 'planification', object_id, object_class, key_tab);
  } } ); 
}

refreshDossierSoin = function(mode_dossier, chapitre, force_refresh){
  if(!window[chapitre+'SoinLoaded'] || force_refresh) {
    PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, mode_dossier, null, null, null, chapitre);
    window[chapitre+'SoinLoaded'] = true;
  }
}

addCibleTransmission = function(sejour_id, object_class, object_id, libelle_ATC) {
  addTransmission(sejour_id, '{{$app->user_id}}', null, object_id, object_class, libelle_ATC);
}


editPerf = function(prescription_line_mix_id, date, mode_dossier, sejour_id){
  var url = new Url("dPprescription", "edit_perf_dossier_soin");
  url.addParam("prescription_line_mix_id", prescription_line_mix_id);
  url.addParam("date", date);
  url.addParam("mode_dossier", mode_dossier);
  url.addParam("sejour_id", sejour_id);
  url.popup(800,600,"Pefusion");
}

submitPosePerf = function(oFormPerf){
  if(confirm('Etes vous sur de vouloir poser la perfusion ?')){
	  $V(oFormPerf.date_pose, 'current');
	  $V(oFormPerf.time_pose, 'current');
	  submitFormAjax(oFormPerf, 'systemMsg', { onComplete: function(){ 
	    PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}', document.click.nb_decalage.value,'{{$mode_dossier}}',oFormPerf.prescription_line_mix_id.value,'CPrescriptionLineMix','');
	  } } )
  }
}


submitRetraitPerf = function(oFormPerf){
  if(confirm('Etes vous sur de vouloir retirer définitivement la perfusion ?')){
	  $V(oFormPerf.date_retrait, 'current');
	  $V(oFormPerf.time_retrait, 'current');
	  submitFormAjax(oFormPerf, 'systemMsg', { onComplete: function(){ 
	    PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}', document.click.nb_decalage.value,'{{$mode_dossier}}',oFormPerf.prescription_line_mix_id.value,'CPrescriptionLineMix','');
	  } } )
	}
}


viewLegend = function(){
  var url = new Url("dPhospi", "vw_lengende_dossier_soin");
  url.modal({width: 400, height: 200});
}

calculSoinSemaine = function(date, prescription_id){
  var url = new Url("dPprescription", "httpreq_vw_dossier_soin_semaine");
  url.addParam("date", date);
  url.addParam("prescription_id", prescription_id);
  url.requestUpdate("semaine");
}

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
  window['inscriptionSoinLoaded'] = false;
  
  window['injSoinLoaded'] = false;
  {{if "dPprescription"|module_active}}
		{{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
		{{foreach from=$specs_chapitre->_list item=_chapitre}}
		  window['{{$_chapitre}}SoinLoaded'] = false;
		{{/foreach}}
  {{/if}}
  if(tabs){
    if(tabs.activeLink){
      tabs.activeLink.up().onmousedown();
    } else {
      if($('tab_categories') && $('tab_categories').down()){
        $('tab_categories').down().onmousedown();
        tabs.setActiveTab($('tab_categories').down().down().key);
      }
    }
  }
}

updateTasks = function(sejour_id){
  var url = new Url("soins", "ajax_vw_tasks_sejour");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("tasks");
}

showDebit = function(div, color){
  $("_perfusion").select("."+div.down().className).each(function(elt){
    elt.setStyle( { backgroundColor: '#'+color } );
  });
}

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
}

function updateNbTrans(sejour_id) {
  var url = new Url("dPhospi", "ajax_count_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.requestJSON(function(count)  {
    var nb_trans = $("nb_trans");
    nb_trans.up("a").setClassName("empty", !count);
    nb_trans.update("("+count+")");
  });
}

{{if "dPprescription"|module_active}}
  prescriptions_ids = {{$multiple_prescription|@json}};
{{/if}}

Main.add(function () {
  {{if !"dPprescription"|module_active || $multiple_prescription|@count <= 1}}
  
	{{if "dPprescription"|module_active}}
	PlanSoins.init({
    composition_dossier: {{$composition_dossier|@json}}, 
    date: "{{$date}}", 
    manual_planif: "{{$manual_planif}}",
    bornes_composition_dossier:  {{$bornes_composition_dossier|@json}},
    nb_postes: {{$bornes_composition_dossier|@count}}
  });
  {{else}}
	  // Si prescription non installé, chargement des taches en premier
	  updateTasks('{{$sejour->_id}}');
	{{/if}}
  loadSuivi('{{$sejour->_id}}');
  
  // Deplacement du dossier de soin
  if($('plan_soin')){
    PlanSoins.moveDossierSoin($('tbody_date'));
  }
  
  new Control.Tabs('tab_dossier_soin');
  if($('tab_categories')){
    tabs = Control.Tabs.create('tab_categories', true);
  }
  refreshTabState();
  
  document.observe("mousedown", function(e){
   // fait crasher IE quand ouvert dans une iframe dans un showModalDialog:
   // l'evenement est lancé sur la balise html et ca plante
    try {
      if (!Event.element(e).up(".tooltip")){
        $$(".tooltip").invoke("hide");
      }
    } catch(e) {}
  });

  {{if !$hide_close}}
    if(window.modalWindow){
      $('modal_button').show();
    }
  {{/if}}
 
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
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="" />
  <input type="hidden" name="prise_id" value="" />
  <input type="hidden" name="datetime" value="" />
  <input type="hidden" name="nb_hours" value="" />
  <input type="hidden" name="quantite" value="" />
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
{{else}}

  <form name="adm_multiple" action="?" method="get">
    <input type="hidden" name="_administrations">
    <input type="hidden" name="_administrations_mix">
  </form>
  
  <form name="click" action="?" method="get">
    <input type="hidden" name="nb_decalage" value="{{$nb_decalage}}" />
  </form>
  
  <form name="addPlanif" action="" method="post">
    <input type="hidden" name="dosql" value="do_administration_aed" />
    <input type="hidden" name="m" value="dPprescription" />
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
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_administrations_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="decalage" value="" />
    <input type="hidden" name="administrations_ids" value=""/>
    <input type="hidden" name="planification" value="1" />
  </form>
  
  <form name="addManualPlanifPerf" action="" method="post">
    <input type="hidden" name="dosql" value="do_planif_line_mix_aed" />
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
    <input type="hidden" name="datetime" value="" />
    <input type="hidden" name="planification_systeme_id" value="" />
    <input type="hidden" name="prescription_line_mix_id" value="" />
    <input type="hidden" name="original_datetime" value="" />
  </form>
      
    <table class="tbl">
      <tr>
        <th colspan="10" class="title text">
           <span style="float: right">
             {{if !$hide_close}}
               <button type="button" class="cancel" style="float: right; display: none;"
                       onclick="modalWindow.close(); 
							          {{if "dPprescription"|module_active}}
												if(window.refreshLinePancarte){ refreshLinePancarte('{{$prescription_id}}'); }
                        if(window.refreshLineSejour){ refreshLineSejour('{{$sejour->_id}}'); }
												{{/if}}" id="modal_button">
								 {{tr}}Close{{/tr}}
							 </button>
             {{/if}}
           </span>
           <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
            {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
           </a>
           
           <h2 style="color: #fff; font-weight: bold;">
             <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_ref_patient->_guid}}')">
               {{$sejour->_ref_patient->_view}}
             </span>
             -
            <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">{{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
             <br /> 
             <span style="font-size: 0.7em;">{{$sejour->_ref_curr_affectation->_ref_lit}}</span>
            {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
            {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
            {{assign var=sejour_id value=$sejour->_id}}
            {{mb_include module="soins" template="inc_vw_antecedent_allergie"}}
            
            
            {{if $dossier_medical->_id && $dossier_medical->_count_allergies}}
              <script type="text/javascript">
                ObjectTooltip.modes.allergies = {  
                  module: "patients",
                  action: "ajax_vw_allergies",
                  sClass: "tooltip"
                };
             
              </script> 
              <img src="images/icons/warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_ref_patient->_guid}}', 'allergies');" />
            {{/if}}
          
           </h2>
        </th>
      </tr>
      {{mb_include module=soins template=inc_infos_patients_soins}}
    </table>
    
    <ul id="tab_dossier_soin" class="control_tabs">
    	{{if "dPprescription"|module_active}}
      <li onmousedown="PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date}}','','administration','','','','med', '{{$hide_close}}'); refreshTabState();"><a href="#jour">Journée</a></li>
      <li onmousedown="calculSoinSemaine('{{$date}}','{{$prescription_id}}');"><a href="#semaine">Semaine</a></li>
				{{if $conf.dPprescription.CPrescription.show_perop_suivi_soins}}
				  <li onmousedown="PlanSoins.showPeropAdministrations('{{$prescription_id}}')"><a href="#perop_adm" {{if $count_perop_adm == 0}}class="empty"{{/if}}>Perop {{if $count_perop_adm}}<small>({{$count_perop_adm}})</small>{{/if}}</a></li>
				{{/if}}
      {{/if}}
			<li onmousedown="updateTasks('{{$sejour->_id}}');"><a href="#tasks">Tâches</a></li>
      <li onmousedown="loadSuivi('{{$sejour->_id}}')"><a href="#dossier_suivi">Trans. <span id="nb_trans"></span> / Obs. / Consult.{{if $conf.soins.constantes_show}} / Const.{{/if}}</a></li>
    </ul>
    
    <span style="float: right;">
      <button type="button" class="print"
            onclick="{{if isset($prescription|smarty:nodefaults)}}Prescription.printOrdonnance('{{$prescription->_id}}');{{/if}}" />Ordonnance</button>
    </span>
   
   <hr class="control_tabs" />
    
    <div id="jour" style="display:none">
    
    {{if "dPprescription"|module_active && $prescription_id}}
      <h1 style="text-align: center;">
            <button type="button" 
                   class="left notext {{if $sejour->_entree >= $bornes_composition_dossier|@reset|@reset}}opacity-50{{/if}}" 
                   {{if $sejour->_entree < $bornes_composition_dossier|@reset|@reset}}onclick="PlanSoins.loadTraitement('{{$sejour->_id}}','{{$prev_date}}', null, null, null, null, null, null, '1', '{{$hide_close}}');"{{/if}}
                   ></button>
    
         Plan de soins du {{$date|@date_format:"%d/%m/%Y"}}       
         
         {{foreach from=$prescription->_jour_op item=_info_jour_op}}
           (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
         {{/foreach}}
         <form name="changeDateDossier" method="get" action="?" onsubmit="return false" style="font-size: 11px">
           <input type="hidden" name="date" class="date" value="{{$date}}" onchange="PlanSoins.loadTraitement('{{$sejour->_id}}',this.value,'','administration', null, null, null, null, '1', '{{$hide_close}}');"/>
         </form>
         <button type="button"
                 class="right notext {{if $sortie_sejour <= $bornes_composition_dossier|@end|@end}}opacity-50{{/if}}"
                 {{if $sortie_sejour > $bornes_composition_dossier|@end|@end}}onclick="PlanSoins.loadTraitement('{{$sejour->_id}}','{{$next_date}}','','administration', null, null, null, null, '1', '{{$hide_close}}');"{{/if}}
                 ></button>
      </h1>
            
      <table style="width: 100%">
         <tr>
          <td>
            <button type="button" class="print" onclick="PlanSoins.printBons('{{$prescription_id}}');" title="{{tr}}Print{{/tr}}">
              Bons
            </button>
            <button type="button" class="print" onclick="Prescription.viewFullAlertes('{{$prescription_id}}')" title="{{tr}}Print{{/tr}}">
              Alertes 
            </button>
            <button type="button" class="tick" onclick="PlanSoins.applyAdministrations();" id="button_administration">
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
          <td style="white-space: nowrap;" class="narrow">
            <!-- Affichage des onglets du dossier de soins -->
            {{mb_include module="dPprescription" template="inc_vw_tab_dossier_soins"}}
          </td>
          <td>
            <!-- Affichage du contenu du dossier de soins -->
            {{mb_include module="dPprescription" template="inc_vw_content_dossier_soins"}}
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
		{{if $conf.dPprescription.CPrescription.show_perop_suivi_soins}}
      <div id="perop_adm" style="display: none"></div>
		{{/if}}
    <div id="tasks" style="display:none"></div>
    <div id="dossier_suivi" style="display:none"></div>
  {{/if}}
{{else}}
  <div class="small-info">
    Veuillez sélectionner un séjour pour pouvoir accéder au suivi de soins.
  </div>
{{/if}}