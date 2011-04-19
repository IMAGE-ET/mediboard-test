{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
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
    Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, 'planification', object_id, object_class, key_tab);
  } } ); 
}

refreshDossierSoin = function(mode_dossier, chapitre, force_refresh){
  if(!window[chapitre+'SoinLoaded'] || force_refresh) {
    Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, mode_dossier, null, null, null, chapitre);
    window[chapitre+'SoinLoaded'] = true;
  }
}

addCibleTransmission = function(object_class, object_id, libelle_ATC) {
  addTransmission('{{$sejour->_id}}', '{{$app->user_id}}', null, object_id, object_class, libelle_ATC);
}

addAdministrationPerf = function(prescription_line_mix_id, date, hour, time_prevue, mode_dossier, sejour_id){
  var url = new Url("dPprescription", "httpreq_add_administration_perf");
  url.addParam("prescription_line_mix_id", prescription_line_mix_id);
  url.addParam("date", date);
  url.addParam("hour", hour);
  url.addParam("time_prevue", time_prevue);
  url.addParam("mode_dossier", mode_dossier);
  url.addParam("sejour_id", sejour_id);
  url.addParam("date_sel", "{{$date}}");
  url.popup(800,600,"Administration d'une prescription_line_mix");
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
  $V(oFormPerf.date_pose, 'current');
  $V(oFormPerf.time_pose, 'current');
  submitFormAjax(oFormPerf, 'systemMsg', { onComplete: function(){ 
    Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}', document.click.nb_decalage.value,'{{$mode_dossier}}',oFormPerf.prescription_line_mix_id.value,'CPrescriptionLineMix','');
  } } )
}

submitRetraitPerf = function(oFormPerf){
  $V(oFormPerf.date_retrait, 'current');
  $V(oFormPerf.time_retrait, 'current');
  submitFormAjax(oFormPerf, 'systemMsg', { onComplete: function(){ 
    Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}', document.click.nb_decalage.value,'{{$mode_dossier}}',oFormPerf.prescription_line_mix_id.value,'CPrescriptionLineMix','');
  } } )
}


viewLegend = function(){
  var url = new Url("dPhospi", "vw_lengende_dossier_soin");
  url.modale("Légende");
}

viewDossier = function(sejour_id){
  var url = new Url("dPprescription", "vw_dossier_cloture");
  url.addParam("sejour_id", sejour_id);
  url.popup(800,600,"Dossier cloturé");
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
  {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
	{{foreach from=$specs_chapitre->_list item=_chapitre}}
    window['{{$_chapitre}}SoinLoaded'] = false;
	{{/foreach}}
	
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
        Prescription.loadTraitement(sejour_id,'{{$date}}','','administration');
      }
      // Refresh de la partie plan de soin
      if($('semaine').visible()){
        calculSoinSemaine('{{$date}}', '{{$prescription_id}}');
      }
    }
    if ($('dossier_suivi').visible()) {
      loadSuivi(sejour_id);
    }
    updateNbTrans(sejour_id);
  } });
}

updateNbTrans = function(sejour_id) {
  var url = new Url("dPhospi", "ajax_count_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.requestJSON(function(elt)  {
    var nb_trans = $("nb_trans");
    if (!elt) {
      nb_trans.up().addClassName("empty");
    }
    else {
      nb_trans.up().removeClassName("empty")
    }
    nb_trans.update("("+elt+")");
  });
}

Main.add(function () {

  PlanSoins.init({
    composition_dossier: {{$composition_dossier|@json}}, 
    date: "{{$date}}", 
    manual_planif: "{{$conf.dPprescription.CPrescription.manual_planif}}",
    bornes_composition_dossier:  {{$bornes_composition_dossier|@json}},
		nb_postes: {{$bornes_composition_dossier|@count}}
  });

  if(window.loadSuivi){
	  loadSuivi('{{$sejour->_id}}');
	}

  updateNbTrans('{{$sejour->_id}}');
  
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
    if (!Event.element(e).up(".tooltip")){
		  $$(".tooltip").invoke("hide");
  	}
  });
	
	if(window.modalWindow){
	  $('modal_button').show();
	}
});

</script>

<form name="adm_multiple" action="?" method="get">
  <input type="hidden" name="_administrations">
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

	<table class="tbl">
	  <tr>
	    <th colspan="10" class="title text">
	    	 <span style="float: right">
					 <button type="button" class="cancel" style="float: right; display: none;" onclick="modalWindow.close(); if(window.refreshLinePancarte){ refreshLinePancarte('{{$prescription_id}}'); }" id="modal_button">{{tr}}Close{{/tr}}</button>
	 	 	     <button type="button" class="print" style="float: right" onclick="viewDossier('{{$sejour->_id}}');">Dossier</button>
	    	 </span>
	       <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
	        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
	       </a>
				 
				 <h2 style="color: #fff; font-weight: bold;">
		      {{$sejour->_ref_patient->_view}}
					<span style="font-size: 0.7em;"> - {{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
				 </h2>
				
			</th>
	  </tr>
	  {{mb_include module=dPprescription template=inc_infos_patients_soins}}
	</table>
	
	<ul id="tab_dossier_soin" class="control_tabs">
	  <li onmousedown="Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}','','administration','','','','med'); refreshTabState();"><a href="#jour">Journée</a></li>
	  <li onmousedown="calculSoinSemaine('{{$date}}','{{$prescription_id}}');"><a href="#semaine">Semaine</a></li>
		<li onmousedown="updateTasks('{{$sejour->_id}}');"><a href="#tasks">Tâches</a></li>
    <li onmousedown="loadSuivi('{{$sejour->_id}}')"><a href="#dossier_suivi">{{tr}}CMbObject-back-transmissions{{/tr}} <span id="nb_trans"></span></a></li>
	</ul>
	<hr class="control_tabs" />
	
	<div id="jour" style="display:none">
	
	{{if $prescription_id}}
		<h1 style="text-align: center;">
		 		  <button type="button" 
					       class="left notext" 
								 {{if $sejour->_entree|iso_date < $date}}onclick="Prescription.loadTraitement('{{$sejour->_id}}','{{$prev_date}}', null, null, null, null, null, null, '1');"{{/if}}
					       {{if $sejour->_entree|iso_date >= $date}}style="opacity: 0.5;"{{/if}}></button>	
	     Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}
			 <button type="button"
			         class="right notext"
							 {{if $sejour->_sortie|iso_date > $date}}onclick="Prescription.loadTraitement('{{$sejour->_id}}','{{$next_date}}','','administration', null, null, null, null, '1');"{{/if}}
							 {{if $sejour->_sortie|iso_date <= $date}}style="opacity: 0.5;"{{/if}}></button>
		</h1>
		 
		 {{if $date != $today}}
			 <div class="small-warning">
			 Attention, le dossier de soin que vous êtes en train de visualiser n'est pas celui de la journée courante
			 </div>
		 {{/if}}
		
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
	<div id="tasks" style="display:none"></div>
  <div id="dossier_suivi" style="display:none"></div>
{{else}}
  <div class="small-info">
    Veuillez sélectionner un séjour pour pouvoir accéder au suivi de soins.
  </div>
{{/if}}