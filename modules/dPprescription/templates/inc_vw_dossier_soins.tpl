<script type="text/javascript">

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
			    _td = td.id.split("_");
			    line_id = _td[1];
			    line_class = _td[2];
			    unite_prise = _td[3];
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
  element = element_id.split("_");
  original_date = element[3]+" "+element[4]+":00:00";
  quantite = element[5];
  planification_id = element[6];

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
  
  prise_id = !isNaN(key_tab) ? unite_prise : '';
  unite_prise = isNaN(key_tab) ? unite_prise : '';

  $V(oForm.unite_prise, unite_prise);
  $V(oForm.prise_id, prise_id);
	$V(oForm.quantite, quantite);

  dateTime = date+" "+time
  
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
	    loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, 'planification', object_id, object_class, key_tab);
	  } } ); 
  }
}

refreshDossierSoin = function(mode_dossier, chapitre){
  if(!window[chapitre+'SoinLoaded']) {
    loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, mode_dossier, null, null, null, chapitre);
    window[chapitre+'SoinLoaded'] = true;
  }
}

printDossierSoin = function(prescription_id){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_plan_soin_pdf");
  url.addParam("prescription_id", prescription_id);
  url.popup(900, 600, "Plan de soin");
}

addCibleTransmission = function(object_class, object_id, view) {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, object_class);
  $V(oForm.object_id, object_id);
  oDiv.innerHTML = view;
  oForm.text.focus();
}

addAdministration = function(line_id, quantite, key_tab, object_class, date, heure, administrations, planification_id) {
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_administration");
  url.addParam("line_id",  line_id);
  url.addParam("quantite", quantite);
  url.addParam("key_tab", key_tab);
  url.addParam("object_class", object_class);
  url.addParam("date", date);
  url.addParam("heure", heure);
  url.addParam("administrations", administrations);
  url.addParam("planification_id", planification_id);
  url.addParam("date_sel", "{{$date}}");
  url.addParam("mode_dossier", $V(document.mode_dossier_soin.mode_dossier));
  url.popup(500,400,"Administration");
}

editPerf = function(perfusion_id, date, mode_dossier, sejour_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "edit_perf_dossier_soin");
  url.addParam("perfusion_id", perfusion_id);
  url.addParam("date", date);
  url.addParam("mode_dossier", mode_dossier);
  url.addParam("sejour_id", sejour_id);
  url.popup(500,400,"Pefusion");
}

toggleSelectForAdministration = function (element, line_id, quantite, key_tab, object_class, date, heure, administrations) {
  element = $(element);
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
      date: date,
      heure: heure/*,
      administrations: administrations*/,
      date_sel: '{{$date}}'
    };
  }
}

applyAdministrations = function () {
  var administrations = {};
  $('plan_soin').select('div.administration-selected').each(function(element) {
    var adm = element._administration;
    administrations[adm.line_id+'_'+adm.key_tab+'_'+adm.date+'_'+adm.heure] = adm;
  });
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_multiple_administrations");
  url.addObjectParam("adm", administrations);
  url.addParam("mode_dossier", $V(document.mode_dossier_soin.mode_dossier));
  url.popup(700, 600, "Administrations multiples");
}

viewLegend = function(){
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_lengende_dossier_soin");
  url.popup(300,150, "Légende");
}

viewDossier = function(prescription_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "vw_dossier_cloture");
  url.addParam("prescription_id", prescription_id);
  url.popup(500,500,"Dossier cloturé");
}

calculSoinSemaine = function(date, prescription_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_dossier_soin_semaine");
  url.addParam("date", date);
  url.addParam("prescription_id", prescription_id);
  url.requestUpdate("semaine", { waitingText: null } );
}

// Initialisation
var planSoin;
var oFormClick = document.click;
var composition_dossier = {{$composition_dossier|@json}};

window.periodicalBefore = null;
window.periodicalAfter = null;

// Deplacement du dossier de soin
moveDossierSoin = function(arg){
  periode_visible = composition_dossier[oFormClick.nb_decalage.value];
  composition_dossier.each(function(moment){
    listToHide = arg.select('.'+moment);
    listToHide.each(function(elt) { 
      elt.show();
    });  
  });
  composition_dossier.each(function(moment){
    if(moment != periode_visible){
	    listToHide = arg.select('.'+moment);
	    listToHide.each(function(elt) { 
	      elt.hide();
	    });  
    }
  });
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

viewDossierSoin = function(mode_dossier){
  // Dossier en mode Administration
  if(mode_dossier == "administration" || mode_dossier == ""){
    $('button_administration').update("Appliquer les administrations sélectionnées");
    $('plan_soin').select('.colorPlanif').each(function(element){
       element.setStyle( { backgroundColor: '#FFD' } );
    });
    $('plan_soin').select('.draggablePlanif').each(function(element){
       element.removeClassName("draggable");
       element.onmousedown = null;
    });
    $('plan_soin').select('.canDropPlanif').each(function(element){
       element.removeClassName("canDrop");
    });
  }
  
  // Dossier en mode planification
  if(mode_dossier == "planification"){
    $('button_administration').update("Appliquer les planifications sélectionnées");
    $('plan_soin').select('.colorPlanif').each(function(element){
       element.setStyle( { backgroundColor: '#CAFFBA' } );
    });
    $('plan_soin').select('.draggablePlanif').each(function(element){
       element.addClassName("draggable");
       element.onmousedown = function(){
         addDroppablesDiv(element);
       }
    });
    $('plan_soin').select('.canDropPlanif').each(function(element){
       element.addClassName("canDrop");
    });
  }
}

tabs = null;

refreshTabState = function(){
  window['medSoinLoaded'] = false;
  window['perfSoinLoaded'] = false;
  window['injSoinLoaded'] = false;
  {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
	{{foreach from=$specs_chapitre->_list item=_chapitre}}
    window['{{$_chapitre}}SoinLoaded'] = false;
	{{/foreach}}
	
  // Lancement du onclick sur le premier onglet et affichage du premier onglet
  if($('tab_categories') && $('tab_categories').down()){
	  $('tab_categories').down().onclick();
	  tabs.setActiveTab($('tab_categories').down().down().key);
  }
  
  if(document.mode_dossier_soin){
    var oForm = document.mode_dossier_soin;
    oForm.mode_dossier[0].checked = true;
  }
}

Main.add(function () {
	{{if $mode_bloc}}
	  loadSuivi('{{$sejour->_id}}');
	{{/if}}

	// Deplacement du dossier de soin
	if($('plan_soin')){
    moveDossierSoin($('tbody_date'));
	  viewDossierSoin('{{$mode_dossier}}');
	}
	
  {{if !$mode_bloc}}
    new Control.Tabs('tab_dossier_soin');
  {{/if}}
  tabs = Control.Tabs.create('tab_categories', true);
  
  refreshTabState();
});

</script>

<form name="click">
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
    <th colspan="10" class="title">
       <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
       </a>
	    {{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
  </tr>
  <tr>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=poids}}:
      {{if $patient->_ref_constantes_medicales->poids}}
        {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
      {{else}}??{{/if}}
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient field=naissance}}: 
      {{mb_value object=$patient field=naissance}} ({{$patient->_age}} ans)
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=taille}}:
      {{if $patient->_ref_constantes_medicales->taille}}
        {{mb_value object=$patient->_ref_constantes_medicales field=taille}} cm
      {{else}}??{{/if}}
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=_imc}}:
      {{if $patient->_ref_constantes_medicales->_imc}}
        {{mb_value object=$patient->_ref_constantes_medicales field=_imc}}
      {{else}}??{{/if}}
    </td>
  </tr>
</table>


{{if !$mode_bloc}}
	<ul id="tab_dossier_soin" class="control_tabs">
	  <li onclick="loadTraitement('{{$sejour->_id}}','{{$date}}','','administration','','','','med'); refreshTabState();"><a href="#jour">Administration</a></li>
	  <li onclick="calculSoinSemaine('{{$date}}','{{$prescription_id}}');"><a href="#semaine">Plan</a></li>
	</ul>
  <hr class="control_tabs" />
{{/if}}

<div id="jour" {{if !$mode_bloc}}style="display:none"{{/if}}>

{{if $prescription_id}}
  {{if !$mode_bloc}}
    <button type="button" class="search" style="float: right" onclick="viewDossier('{{$prescription_id}}');">Dossier cloturé</button>

	 <h1 style="text-align: center">
	   <a href="#" {{if $sejour->_entree|date_format:"%Y-%m-%d" < $date}}onclick="loadTraitement('{{$sejour->_id}}','{{$prev_date}}');"{{/if}}>
	     <img src="images/icons/prev.png" alt="" {{if $sejour->_entree|date_format:"%Y-%m-%d" >= $date}}style="opacity: 0.5; -moz-opacity: 0.5;"{{/if}} />
	   </a>
	   Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}
	   <a href="#" {{if $sejour->_sortie|date_format:"%Y-%m-%d" > $date}}onclick="loadTraitement('{{$sejour->_id}}','{{$next_date}}','','administration');"{{/if}}>
	     <img src="images/icons/next.png" alt="" {{if $sejour->_sortie|date_format:"%Y-%m-%d" <= $date}}style="opacity: 0.5; -moz-opacity: 0.5;"{{/if}} />
	   </a>
	 </h1>
	 
	 {{if $date != $today}}
	 <div class="small-warning">
	 Attention, le dossier de soin que vous êtes en train de visualiser n'est pas celui de la journée courante
	 </div>
	 {{/if}}
	 
	 {{/if}}
	<table style="width: 100%">
	  <tr>
	    <td>
	    {{if !$mode_bloc}}
	      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}');" title="{{tr}}Print{{/tr}}">
		      Imprimer la feuille de soins immédiate
	      </button>
	    {{/if}}
        <button type="button" class="tick" onclick="applyAdministrations();" id="button_administration">
        </button>
	    </td>
	    <td style="text-align: center">
	      <form name="mode_dossier_soin">
	        <label>
	          <input type="radio" name="mode_dossier" value="administration" {{if $mode_dossier == "administration" || $mode_dossier == ""}}checked="checked"{{/if}} 
	          			 onclick="viewDossierSoin('administration');"/>Administration
          </label>
          <label>
            <input type="radio" name="mode_dossier" value="planification" {{if $mode_dossier == "planification"}}checked="checked"{{/if}} 
            			 onclick="viewDossierSoin('planification');" />Planification
          </label>
       </form>
	    </td>
	    <td style="text-align: right">
	      <button type="button" class="search" onclick="viewLegend()">Légende</button>
	    </td>
	  </tr>
	</table>

	{{assign var=transmissions value=$prescription->_transmissions}}	  

  <table style="width: 100%">
	  <tr>
	    <td style="width: 1%">
		 	 <table>
			 	 <tr>
					 <td>
					   <ul id="tab_categories" class="control_tabs_vertical">
						    {{if $prescription->_ref_perfusions_for_plan|@count}}
						      <li onclick="refreshDossierSoin(null, 'perf');"><a href="#_perf">Perfusions</a></li>
						    {{/if}}
								
								{{if $prescription->_ref_injections_for_plan|@count}}
								<li onclick="refreshDossierSoin(null, 'inj');"><a href="#_inj">Injections</a></li>
						    {{/if}}
						    
						    {{if $prescription->_ref_lines_med_for_plan|@count}}
						      <li onclick="refreshDossierSoin(null, 'med');"><a href="#_med">Médicaments</a></li>
						    {{/if}}
							  {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
							  {{foreach from=$specs_chapitre->_list item=_chapitre}}
							    {{if @is_array($prescription->_ref_lines_elt_for_plan.$_chapitre)}}
							    <li onclick="refreshDossierSoin(null, '{{$_chapitre}}');"><a href="#_cat-{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
							    {{/if}}
							  {{/foreach}}
							</ul>	
				 	 	</td>
			 	 	</tr>
		 	 	</table>
		 	 	</td>
		 	 	<td>
				<table class="tbl" id="plan_soin">
				<tbody id="tbody_date">
				  {{if $prescription->_ref_lines_med_for_plan|@count || $prescription->_ref_lines_elt_for_plan|@count || 
				  		 $prescription->_ref_perfusions_for_plan|@count || $prescription->_ref_injections_for_plan|@count}}
					  <tr>
					    <th rowspan="2">Catégorie</th>
					    <th rowspan="2">Cond.</th>
					    <th rowspan="2">Libellé</th>
					    <th rowspan="2">Posologie</th>
					    	    
      
					    {{foreach from=$tabHours key=_date item=_hours_by_moment}}
				        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
				          <th class="{{$_date}}-{{$moment_journee}}"
				              colspan="{{if $moment_journee == 'soir'}}{{$count_soir}}{{/if}}
				          						 {{if $moment_journee == 'nuit'}}{{$count_nuit}}{{/if}}
				          						 {{if $moment_journee == 'matin'}}{{$count_matin}}{{/if}}">
				          	
				          			 
					          <a href="#1" onclick="showBefore()" style="float: left" onmousedown="periodicalBefore = new PeriodicalExecuter(showBefore, 0.2);" onmouseup="periodicalBefore.stop();">
							        <img src="images/icons/prev.png" alt="&lt;"/>
							      </a>				
					          <a href="#1" onclick="showAfter()" style="float: right" onmousedown="periodicalAfter = new PeriodicalExecuter(showAfter, 0.2);" onmouseup="periodicalAfter.stop();">
								      <img src="images/icons/next.png" alt="&gt;" />
							      </a>		 
					          <strong>{{$moment_journee}} du {{$_date|date_format:"%d/%m"}}</strong>
									</th>
						    {{/foreach}} 
					    {{/foreach}}
					    <th colspan="2">Sign.</th>
					  </tr>
					  <tr>
					    <th></th>
					    {{foreach from=$tabHours key=_date item=_hours_by_moment}}
				        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
				          {{foreach from=$_dates key=_date_reelle item=_hours}}
				            {{foreach from=$_hours key=_heure_reelle item=_hour}}
				              <th class="{{$_date}}-{{$moment_journee}}" 
				                  style='width: 30px; text-align: center; 
			                  {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
			                  {{$_hour}}h
				                {{if array_key_exists("$_date $_hour:00:00", $operations)}}
				                  {{assign var=_hour_op value="$_date $_hour:00:00"}}
				                  <a style="color: white; font-weight: bold; font-style: normal;" href="#" title="Intervention à {{$operations.$_hour_op|date_format:'%Hh%M'}}">Interv.</a>
				                {{/if}}
				              </th>   
						        {{/foreach}}
						      {{/foreach}}
						    {{/foreach}} 
					    {{/foreach}}
					    <th></th>
					    <th>Dr</th>
					    <th>Ph</th>
					  </tr>
			    {{/if}}
				  </tbody>
				  
			    <!-- Affichage des perfusions -->
					<tbody id="_perf" style="display:none;"></tbody>	
				  <!-- Affichage des injectables -->
				  <tbody id="_inj" style="display: none;"></tbody>
					<!-- Affichage des medicaments -->
				  <tbody id="_med" style="display: none;"></tbody>			
				  <!-- Affichage des elements -->
				  {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap name="foreach_element"}}
				    {{if !$smarty.foreach.foreach_element.first}}
				      </tbody>
				    {{/if}}
					  <tbody id="_cat-{{$name_chap}}" style="display: none;">  

				  {{/foreach}}
					</tbody>
					 
				</table>
	    </td>
	  </tr>
  </table>
{{else}}
  <div class="big-info">
	Ce dossier ne possède pas de prescription de séjour
  </div>
{{/if}}
</div>

{{if $mode_bloc}}
  <!-- Affichage des transmissions dans le cas de l'affichage au bloc -->
  <div id="dossier_suivi"></div>
{{else}}
  <div id="semaine" style="display:none"></div>
{{/if}}