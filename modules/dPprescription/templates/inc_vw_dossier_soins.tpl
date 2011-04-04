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

selColonne = function(hour){
	$('plan_soin').select('div.non_administre:not(.perfusion), div.a_administrer:not(.perfusion)').each(function(oDiv){
	  if(oDiv.up("tbody").visible() && oDiv.up("td").hasClassName(hour)){
	    if(Object.isFunction(oDiv.onclick)){
			  oDiv.onclick();
			}
	  }
	});
}

viewFicheATC = function(fiche_ATC_id){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_fiche_ATC");
  url.addParam("fiche_ATC_id", fiche_ATC_id);
  url.popup(700, 550, "Fiche ATC");  
}

oDragOptions = {
  constraint: 'horizontal',
  revert: true,
  ghosting: true,
  starteffect : function(element) {	
    new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 }); 
    element.hide();
  },
  reverteffect: function(element, top_offset, left_offset) {
    var dur = Math.sqrt(Math.abs(top_offset^2)+Math.abs(left_offset^2))*0.02;
    element._revert = new Effect.Move(element, { 
      x: -left_offset, 
      y: -top_offset, 
      duration: 0
    } );
   // Suppression des zones droppables sur le revert
   Droppables.drops.clear(); 
   element.show();
  },
  endeffect: function(element) { 
    new Effect.Opacity(element, { duration:0.2, from:0.7, to:1.0 } ); 
  }       
}

addDroppablesDiv = function(draggable){
  $('plan_soin').select('.before').each(function(td_before) {
    td_before.onmouseover = function(){
      timeOutBefore = setTimeout(showBefore, 1000);
    }
  });
  $('plan_soin').select('.after').each(function(td_after) {
    td_after.onmouseover = function(){
      timeOutAfter = setTimeout(showAfter, 1000);
    }
  });
  
  $(draggable).up(1).select('td').each(function(td) {
	  if(td.hasClassName("canDrop")){
	    Droppables.add(td.id, {
	      onDrop: function(element) {
			    var _td = td.id.split("_");
			    line_id = _td[1];
			    line_class = _td[2];
			    unite_prise = td.getAttribute("data-uniteprise");
			    date = _td[4];
			    hour = _td[5];
			    
				  // Hack pour corriger le probleme des planifications sur aucune prise prevue
				  if(_td[3] == 'aucune' && _td[4] == 'prise'){
				    unite_prise = "aucune_prise";
				    date = _td[5];
				    hour = _td[6];
				  }
				  // Ajout de la planification
	        addPlanification(date, hour+":00:00", unite_prise, line_id, line_class, element.id);
	        // Suppression des zones droppables
	        Droppables.drops.clear(); 
				  $('plan_soin').select('.before').each(function(td_before) {
				    td_before.onmouseover = null;
				  });
				  $('plan_soin').select('.after').each(function(td_after) {
				    td_after.onmouseover = null;
				  });
	      },
	      hoverclass:'soin-selected'
	    } );
    } 
  });
}

addPlanification = function(date, time, key_tab, object_id, object_class, element_id){
  // Split de l'element_id
  var element = element_id.split("_");
  var original_date = element[3]+" "+element[4]+":00:00";
  var quantite = element[5];
  var planification_id = element[6];

	// Hack pour corriger le probleme des planifications sur aucune prise prevue
	if(element[2] == 'aucune' && element[3] == 'prise'){
	  original_date = element[4]+" "+element[5]+":00:00";
	  quantite = element[6];
    planification_id = element[7];
	}

	var oForm = document.addPlanif;
  $V(oForm.administrateur_id, '{{$app->user_id}}');
  
  $V(oForm.object_id, object_id);
  $V(oForm.object_class, object_class);
  
  var prise_id = !isNaN(key_tab) ? key_tab : '';
  var unite_prise = isNaN(key_tab) ? key_tab : '';

  $V(oForm.unite_prise, unite_prise);
  $V(oForm.prise_id, prise_id);
	$V(oForm.quantite, quantite);

  var dateTime = date+" "+time;
  
  $V(oForm.dateTime, dateTime);
  if(planification_id){
    $V(oForm.administration_id, planification_id);
    oForm.original_dateTime.writeAttribute("disabled", "disabled");
  } else { 
    oForm.original_dateTime.enable();
    $V(oForm.original_dateTime, original_date);
  }
  
	if(original_date != dateTime){
	  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
	    Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, 'planification', object_id, object_class, key_tab);
	  } } ); 
  }
}

refreshDossierSoin = function(mode_dossier, chapitre, force_refresh){
  if(!window[chapitre+'SoinLoaded'] || force_refresh) {
    Prescription.loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, mode_dossier, null, null, null, chapitre);
    window[chapitre+'SoinLoaded'] = true;
  }
}

printDossierSoin = function(prescription_id){
  var url = new Url("dPprescription", "vw_plan_soin_pdf");
  url.addParam("prescription_id", prescription_id);
  url.popup(900, 600, "Plan de soin");
}

printBons = function(prescription_id){
  var url = new Url("dPprescription", "print_bon");
  url.addParam("prescription_id", prescription_id);
  url.addParam("debut", "{{$date}}");
  url.popup(900, 600, "Impression des bons");
}

addCibleTransmission = function(object_class, object_id, view, libelle_ATC) {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  if(object_id && object_class){
	  $V(oForm.object_class, object_class);
	  $V(oForm.object_id, object_id);
  }
  if(libelle_ATC){
    $V(oForm.libelle_ATC, libelle_ATC);
  }
  oDiv.innerHTML = view;
  oForm.text.focus();
}

addAdministration = function(line_id, quantite, key_tab, object_class, dateTime, administrations, planification_id, multiple_adm) {
  /*
	 Dans le cas des administrations multiples, si on clique sur la case principale, 
	 on selectionne toutes les administrations et on lance la fenetre d'administrations multiples
	*/
	if(multiple_adm == 1){
	  var date_time = dateTime.replace(' ', '_').substring(0,13);
	  $('subadm_'+line_id+'_'+object_class+'_'+key_tab+'_'+date_time).select('div').each(function(e){
		  e.onclick.bind(e)();
		});
		applyAdministrations();
    return;
	}
	
	// On ne permet pas de faire des planifications sur des lignes de medicament
	if(!planification_id && (object_class == "CPrescriptionLineMedicament") && ($V(document.mode_dossier_soin.mode_dossier) == "planification")){
	  return;
	}
	
  var url = new Url("dPprescription", "httpreq_add_administration");
  url.addParam("line_id",  line_id);
  url.addParam("quantite", quantite);
  url.addParam("key_tab", key_tab);
  url.addParam("object_class", object_class);
	url.addParam("dateTime", dateTime);
	url.addParam("administrations", administrations);
  url.addParam("planification_id", planification_id);
  url.addParam("date_sel", "{{$date}}");
  url.addParam("mode_dossier", $V(document.mode_dossier_soin.mode_dossier));
	url.addParam("multiple_adm", multiple_adm);
  url.popup(800,600,"Administration");
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

addInscription = function(datetime, prescription_id){
  var url = new Url("dPprescription", "vw_edit_inscription");
  url.addParam("datetime", datetime);
	url.addParam("prescription_id", prescription_id);
  url.popup(800, 600, "Ajout d'une inscription");
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

toggleSelectForAdministration = function (element, line_id, quantite, key_tab, object_class, dateTime) {	
  element = $(element);

  // si la case est une administration multiple, on selectionne tous les elements à l'interieur
	if(element.hasClassName('multiple_adm')){
	  element.next('div').select('div').invoke('onclick');
	}
	
	if (element._administration) {
    element.removeClassName('administration-selected');
    element._administration = null;
  }
  else {
    element.addClassName('administration-selected');
    element._administration = {
      line_id: line_id,
      quantite: quantite,
      key_tab: key_tab,
      object_class: object_class,
      dateTime: dateTime,
			date_sel: '{{$date}}'
    };
  }
}

applyAdministrations = function () {
  var administrations = {};
   
	$$('div.administration-selected').each(function(element) { 
	  if(!element.hasClassName('multiple_adm')){
		  var adm = element._administration;
      administrations[adm.line_id+'_'+adm.key_tab+'_'+adm.dateTime] = adm; 
		}
	});
	
	$V(getForm("adm_multiple")._administrations, Object.toJSON(administrations));
	
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_multiple_administrations");
  url.addParam("mode_dossier", $V(document.mode_dossier_soin.mode_dossier));
	url.addParam("refresh_popup", "1");
  url.popup(700, 600, "Administrations multiples");
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
var planSoin;
var oFormClick = document.click;
var composition_dossier = {{$composition_dossier|@json}};

window.periodicalBefore = null;
window.periodicalAfter = null;

// Deplacement du dossier de soin
moveDossierSoin = function(element){
  periode_visible = composition_dossier[oFormClick.nb_decalage.value];
  composition_dossier.each(function(moment){
    listToHide = element.select('.'+moment);
    listToHide.each(function(elt) { 
      elt.show();
    });  
  });
  composition_dossier.each(function(moment){
    if(moment != periode_visible){
	    listToHide = element.select('.'+moment);
	    listToHide.each(function(elt) { 
	      elt.hide();
	    });  
    }
  });
  viewDossierSoin(element);
}

timeOutBefore = null;
timeOutAfter = null;

// Deplacement du dossier vers la gauche
showBefore = function(){
  if(oFormClick.nb_decalage.value >= 1){
    oFormClick.nb_decalage.value = parseInt(oFormClick.nb_decalage.value) - 1;
    moveDossierSoin($('plan_soin'));
  }
}
// Deplacement du dossier de soin vers la droite
showAfter = function(){
  if(oFormClick.nb_decalage.value <= 3){
    oFormClick.nb_decalage.value = parseInt(oFormClick.nb_decalage.value) + 1;
    moveDossierSoin($('plan_soin'));
  }
}

viewDossierSoin = function(element){
  // recuperation du mode d'affichage du dossier (administration ou planification)
  mode_dossier = $V(document.mode_dossier_soin.mode_dossier);
  
  // Dossier en mode Administration
  if(mode_dossier == "administration" || mode_dossier == ""){
    $('button_administration').update("Appliquer les administrations sélectionnées");
    element.select('.colorPlanif').each(function(elt){
       elt.setStyle( { backgroundColor: '#FFD' } );
    });
    element.select('.draggablePlanif').each(function(elt){
       elt.removeClassName("draggable");
       elt.onmousedown = null;
    });
    element.select('.canDropPlanif').each(function(elt){
       elt.removeClassName("canDrop");
    });
  }
  
  // Dossier en mode planification
  if(mode_dossier == "planification"){
    $('button_administration').update("Appliquer les planifications sélectionnées");
    element.select('.colorPlanif').each(function(elt){
       elt.setStyle( { backgroundColor: '#CAFFBA' } );
    });
    element.select('.draggablePlanif').each(function(elt){
       elt.addClassName("draggable");
       elt.onmousedown = function(){
         addDroppablesDiv(element);
       }
    });
    element.select('.canDropPlanif').each(function(elt){
       elt.addClassName("canDrop");
    });
  }
}

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

Main.add(function () {
  if(window.loadSuivi){
	  loadSuivi('{{$sejour->_id}}');
	}
	
	// Deplacement du dossier de soin
	if($('plan_soin')){
    moveDossierSoin($('tbody_date'));
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
		<li onmousedown="updateTasks('{{$sejour->_id}}');"><a href="#tasks">Activités</a></li>
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
					<button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}');" title="{{tr}}Print{{/tr}}">
			      Feuille de soins immédiate
		      </button>
		      <button type="button" class="print" onclick="printBons('{{$prescription_id}}');" title="{{tr}}Print{{/tr}}">
		        Bons
		      </button>
					<button type="button" class="print" onclick="Prescription.viewFullAlertes('{{$prescription_id}}')" title="{{tr}}Print{{/tr}}">
					  Alertes	
					</button>
	        <button type="button" class="tick" onclick="applyAdministrations();" id="button_administration">
	        </button>
		    </td>
		    <td style="text-align: center">
		      <form name="mode_dossier_soin" action="?" method="get">
		        <label>
		          <input type="radio" name="mode_dossier" value="administration" {{if $mode_dossier == "administration" || $mode_dossier == ""}}checked="checked"{{/if}} 
		          			 onclick="viewDossierSoin($('plan_soin'));"/>Administration
	          </label>
	          <label>
	            <input type="radio" name="mode_dossier" value="planification" {{if $mode_dossier == "planification"}}checked="checked"{{/if}} 
	            			 onclick="viewDossierSoin($('plan_soin'));" />Planification
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
	<hr />
	<div id="dossier_suivi"></div>
{{else}}
  <div class="small-info">
    Veuillez sélectionner un séjour pour pouvoir accéder au suivi de soins.
  </div>
{{/if}}