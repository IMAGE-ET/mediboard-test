<script type="text/javascript">
		     
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

   element.show();
  },
  endeffect: function(element) { 
    new Effect.Opacity(element, { duration:0.2, from:0.7, to:1.0 } ); 
  }       
}

addDroppablesDiv = function(draggable){
  $("before").onmouseover = function(){ 
    timeOutBefore = setTimeout(showBefore, 1000);
  }
  $("after").onmouseover = function(){ 
    timeOutAfter = setTimeout(showAfter, 1000);
  }
  
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
	        addPlanification(date, hour+":00:00", unite_prise, line_id, line_class, element.id);
	        Droppables.drops.clear(); 
	        $("before").onmouseover = null;
	        $("after").onmouseover = null;
	      },
	      hoverclass:'soin-selected'
	    } );
    } 
  });
}

addPlanification = function(date, time, unite_prise, object_id, object_class, element_id){
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
  
  prise_id = !isNaN(unite_prise) ? unite_prise : '';
  unite_prise = isNaN(unite_prise) ? unite_prise : '';

  $V(oForm.unite_prise, unite_prise);
  $V(oForm.prise_id, prise_id);
	$V(oForm.quantite, quantite);

  dateTime = date+" "+time
  
  $V(oForm.dateTime, dateTime);
  if(planification_id){
    $V(oForm.administration_id, planification_id);
    oForm.original_dateTime.writeAttribute("disabled", "disabled");
  } else { 
    $V(oForm.original_dateTime, original_date);
  }
  
	if(original_date != dateTime){
	  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
	    loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value,'{{$mode_dossier}}');
	  } } ); 
  }
}

refreshDossierSoin = function(mode_dossier){
  loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value, mode_dossier);
}

printDossierSoin = function(prescription_id, date){
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
  url.addParam("mode_dossier", "{{$mode_dossier}}");
  url.popup(500,400,"Administration");
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
  url.addParam("mode_dossier", "{{$mode_dossier}}");
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
moveDossierSoin = function(){
  periode_visible = composition_dossier[oFormClick.nb_decalage.value];
  composition_dossier.each(function(moment){
    listToHide = $('plan_soin').select('.'+moment);
    listToHide.each(function(elt) { 
      elt.show();
    });  
  });
  composition_dossier.each(function(moment){
    if(moment != periode_visible){
	    listToHide = $('plan_soin').select('.'+moment);
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
    moveDossierSoin();
  }
}
// Deplacement du dossier de soin vers la droite
showAfter = function(){
  if(oFormClick.nb_decalage.value <= 3){
    oFormClick.nb_decalage.value = parseInt(oFormClick.nb_decalage.value) + 1;
    moveDossierSoin();
  }
}

Main.add(function () {
	{{if $mode_bloc}}
	  loadSuivi('{{$sejour->_id}}');
	{{/if}}

	// Deplacement du dossier de soin
	if($('plan_soin')){
    moveDossierSoin();
  }

  {{if !$mode_bloc}}
    new Control.Tabs('tab_dossier_soin');
  {{/if}}
  var tabs = Control.Tabs.create('tab_categories', true);
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
    <th colspan="10" class="title">{{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
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
	  <li onclick="loadTraitement('{{$sejour->_id}}','{{$date}}','','administration');"><a href="#jour">Administration</a></li>
	  <li onclick="calculSoinSemaine('{{$date}}','{{$prescription_id}}');"><a href="#semaine">Plan</a></li>
	</ul>
  <hr class="control_tabs" />
{{/if}}

<div id="jour" {{if !$mode_bloc}}style="display:none"{{/if}}>

{{if $prescription_id}}
  {{if !$mode_bloc}}
  <button type="button" class="search" style="float: right" onclick="viewDossier('{{$prescription_id}}');">Dossier cloturé</button>
	 <h2 style="text-align: center">
	    Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}
	 </h2>
	 {{/if}}
	<table style="width: 100%">
	  <tr>
	    <td>
	    {{if !$mode_bloc}}
	      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}','{{$date}}');" title="{{tr}}Print{{/tr}}">
		      Imprimer la feuille de soins immédiate
	      </button>
	    {{/if}}
        <button type="button" class="tick" onclick="applyAdministrations();">
          {{if $mode_dossier == "administration"}}
          Appliquer les administrations séléctionnées
          {{/if}}
          {{if $mode_dossier == "planification"}}
          Appliquer les planifications séléctionnées
          {{/if}}
        </button>
	    </td>
	    <td style="text-align: center">
	      <form name="mode_dossier_soin">
	        <label>
	          <input type="radio" name="mode_dossier" value="administration" {{if $mode_dossier == "administration"}}checked="checked"{{/if}} onclick="refreshDossierSoin('administration')"/>Administration
          </label>
          <label>
            <input type="radio" name="mode_dossier" value="planification" {{if $mode_dossier == "planification"}}checked="checked"{{/if}} onclick="refreshDossierSoin('planification')" />Planification
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
						      <li><a href="#_perf">Perfusions</a></li>
						    {{/if}}
								
								{{if $prescription->_ref_injections_for_plan|@count}}
								<li><a href="#_inj">Injections</a></li>
						    {{/if}}
						    
						    {{if $prescription->_ref_lines_med_for_plan|@count}}
						      <li><a href="#_med">Médicaments</a></li>
						    {{/if}}
							  {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
							  {{foreach from=$specs_chapitre->_list item=_chapitre}}
							    {{if @is_array($prescription->_ref_lines_elt_for_plan.$_chapitre)}}
							    <li><a href="#_cat-{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
							    {{/if}}
							  {{/foreach}}
							</ul>	
				 	 	</td>
			 	 	</tr>
		 	 	</table>
		 	 	</td>
		 	 	<td>
				<table class="tbl" id="plan_soin">
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
				                  style='text-align: center; 
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
			
			    <!-- Affichage des perfusions -->
					<tbody id="_perf" style="display:none;">
					  {{foreach from=$prescription->_ref_perfusions_for_plan item=_perfusion name=foreach_perfusion}}
					    {{include file="../../dPprescription/templates/inc_vw_perf_dossier_soin.tpl" nodebug=true}}
					  {{/foreach}}
					</tbody>
					
					
				  <!-- Affichage des injectables -->
				  <tbody id="_inj" style="display: none;">
				    {{foreach from=$prescription->_ref_injections_for_plan item=inj_cat_ATC key=_key_cat_ATC name="_foreach_cat"}}
				      {{foreach from=$inj_cat_ATC item=inj_line name="_foreach_med"}}
				        {{foreach from=$inj_line key=unite_prise item=inj_line_med name="_foreach_line"}} 
						        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
						            line=$inj_line_med
						            nodebug=true
						            first_foreach=_foreach_med
						            last_foreach=_foreach_line
						            global_foreach=_foreach_cat
						            nb_line=$inj_line|@count
						            type="inj"
						            dosql=do_prescription_line_medicament_aed}}
					      {{/foreach}}
					    {{/foreach}} 		 
				    {{/foreach}}
			    </tbody>
			    
			    
				  
					<!-- Affichage des medicaments -->
				  <tbody id="_med" style="display: none;">
				    {{foreach from=$prescription->_ref_lines_med_for_plan item=_cat_ATC key=_key_cat_ATC name="foreach_cat"}}
				      {{foreach from=$_cat_ATC item=_line name="foreach_med"}}
				        {{foreach from=$_line key=unite_prise item=line_med name="foreach_line"}} 
				          {{if !$line_med->_is_injectable}}
						        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
						            line=$line_med
						            nodebug=true
						            first_foreach=foreach_med
						            last_foreach=foreach_line
						            global_foreach=foreach_cat
						            nb_line=$_line|@count
						            type="med"
						            dosql=do_prescription_line_medicament_aed}} 
						        {{/if}}
					      {{/foreach}}
					    {{/foreach}} 		 
				    {{/foreach}}
			    </tbody>
			    
					
					
				  <!-- Affichage des elements -->
				  {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap name="foreach_element"}}
				       {{if !$smarty.foreach.foreach_element.first}}
				         </tbody>
				       {{/if}}
					     <tbody id="_cat-{{$name_chap}}" style="display: none;">  
							    {{foreach from=$elements_chap key=name_cat item=elements_cat name="foreach_chap"}}
							      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
							      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
							        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 	          
							          {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
							                    line=$element
							                    nodebug=true
							                    first_foreach=foreach_cat
							                    last_foreach=foreach_elt
							                    global_foreach=foreach_chap
							                    nb_line=$_element|@count
							                    dosql=do_prescription_line_element_aed}}
							   {{/foreach}}
							  {{/foreach}}
							{{/foreach}}
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