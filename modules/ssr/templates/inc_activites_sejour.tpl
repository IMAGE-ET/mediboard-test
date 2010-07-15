{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $bilan->technicien_id}} 
<script type="text/javascript">

selectActivite = function(activite) {
  $V(oFormEvenementSSR.prescription_line_element_id, '');
  $V(oFormEvenementSSR._element_id, '');

  $$("button.activite").invoke("removeClassName", "selected");
  $("trigger-"+activite).addClassName("selected");
	
  $$("div.activite").invoke("hide");
  $("activite-"+activite).show();

  // On masque les techncien et on enleve le technicien selectionné
  $$("div.techniciens").invoke("hide").invoke("removeClassName", "selected");
  $$("button.ressource").invoke("removeClassName", "selected");
	$V(oFormEvenementSSR.therapeute_id, '');  
	
	// Suppression des valeurs su select de technicien
	$$("select._technicien_id").each(function(select_tech){
	  $V(select_tech, '');
	});
  
	// Affichage des techniciens correspondants à l'activité selectionnée
	$("techniciens-"+activite).show();

  // On masque les codes Cdarrs
  $$("div.cdarrs").invoke("hide");
	$$("div.type-cdarrs").invoke("hide");
  
  $('div_other_cdarr').hide(); 
	$('other_cdarr').hide();
  $V(oFormEvenementSSR.code, '');
	oFormEvenementSSR._cdarr.checked = false;
  
  // Mise en evidence des elements dans les plannings
  addBorderEvent();
	refreshSelectSeances();
}

selectElement = function(line_id){
  $V(oFormEvenementSSR.line_id, line_id);

  $$("button.line").invoke("removeClassName", "selected");
  $$(".button-type-cdarrs").invoke("removeClassName","selected");
  $("line-"+line_id).addClassName("selected");
  
  $$("div.cdarrs").invoke("hide");
	$$("div.type-cdarrs").invoke("hide");
	
  $V(getForm("editEvenementSSR").cdarr, '');
  $("cdarrs-"+line_id).show();
	$("type-cdarrs-"+line_id).show();
  
  $('div_other_cdarr').show();

  // Deselection de tous les codes cdarrs
  removeCdarrs();

  // Mise en evidence des elements dans les plannings
  addBorderEvent();
	refreshSelectSeances();
}

selectTypeCdarr = function(type_cdarr, line_id, buttonSelected){
  $$('.cdarrs').invoke('hide'); 
	$('cdarrs-'+line_id+'-'+type_cdarr).show();
	
	$$(".button-type-cdarrs").invoke("removeClassName","selected");
	buttonSelected.addClassName("selected");
}

selectTechnicien = function(kine_id, buttonSelected) {
  $V(oFormEvenementSSR.therapeute_id, kine_id);	
	
  $$("button.ressource").invoke("removeClassName", "selected");
  if(buttonSelected){
	  buttonSelected.addClassName("selected");
  }
  	  
  PlanningTechnicien.show(kine_id, null, '{{$bilan->sejour_id}}');
	if($V(oFormEvenementSSR.equipement_id)){
	  PlanningEquipement.show($V(oFormEvenementSSR.equipement_id), '{{$bilan->sejour_id}}');
	}
	refreshSelectSeances();
}

selectEquipement = function(equipement_id) {
  $V(oFormEvenementSSR.equipement_id, equipement_id);
	$$("button.equipement").invoke("removeClassName", "selected");
	if($("equipement-"+equipement_id)){
    $("equipement-"+equipement_id).addClassName("selected");
	}
	if(equipement_id){
  	PlanningEquipement.show(equipement_id,'{{$bilan->sejour_id}}');
	} else {
	  PlanningEquipement.hide();
  }
	refreshSelectSeances();
}

refreshSelectSeances = function(){  
  if($V(oFormEvenementSSR.equipement_id) && 
	   $V(oFormEvenementSSR.therapeute_id) &&
		 $V(oFormEvenementSSR.line_id)){
	
		var url = new Url("ssr", "ajax_vw_select_seances");
		url.addParam("therapeute_id", $V(oFormEvenementSSR.therapeute_id));
		url.addParam("equipement_id", $V(oFormEvenementSSR.equipement_id));
    url.addParam("prescription_line_element_id", $V(oFormEvenementSSR.line_id));
    url.requestUpdate("select-seances", { 
		  onComplete: function(){ 
			  $('seances').show();
				if($V(oFormEvenementSSR.seance_collective)){
				  oFormEvenementSSR.seance_collective_id.show();
				}
			}
		});
	} else {
	  $('seances').hide();
		$V(oFormEvenementSSR.seance_collective, false);
		$V(oFormEvenementSSR.seance_collective_id, '');
	}
}

removeCdarrs = function(){
  oFormEvenementSSR.select('input[name^="cdarrs"]').each(function(e){
    e.checked = false;
  });
	$$('.counts_cdarr').invoke('update','')
}

submitSSR = function(){
  if((oFormEvenementSSR.select('input[name^="cdarrs"]:checked').length == 0) && !$V(oFormEvenementSSR.code) && oFormEvenementSSR.select('input[name^="_cdarrs"]').length == 0){
	  alert("Veuillez selectionner un code SSR");
		return false;
	}
	if(!$V(oFormEvenementSSR.seance_collective) || ($V(oFormEvenementSSR.seance_collective) && !$V(oFormEvenementSSR.seance_collective_id))){
	  if((oFormEvenementSSR.select('input[name^="_days"]:checked').length == 0)){
      alert("Veuillez selectionner au minimum un jour");
      return false;
    }
		if(!$V(oFormEvenementSSR._heure)){
	    alert("Veuillez selectionner une heure");
	    return false;
	  }
    if(!$V(oFormEvenementSSR.duree)){
      alert("Veuillez selectionner une durée");
      return false;
    }
	}
	
	if(!$$("button.equipement.selected").length && !$V(oFormEvenementSSR.equipement_id)){
	  alert("Veuillez selectionner un equipement");
    return false;
	}
	
  return onSubmitFormAjax(oFormEvenementSSR, { onComplete: function(){
		refreshPlanningsSSR();
	  $$(".days").each(function(e){
		  $V(e, '');
		});
		$V(oFormEvenementSSR._heure, '');
		$V(oFormEvenementSSR._heure_da, '');
		$V(oFormEvenementSSR.duree, '');
		$V(oFormEvenementSSR.seance_collective, '');
    $V(oFormEvenementSSR.seance_collective_id, '');
		oFormEvenementSSR.seance_collective_id.hide();
	}} );
}

refreshPlanningsSSR = function(){
  Planification.refreshSejour('{{$bilan->sejour_id}}', true);
	PlanningTechnicien.show($V(oFormEvenementSSR.therapeute_id), null, '{{$bilan->sejour_id}}');
	if($V(oFormEvenementSSR.equipement_id)){
	  PlanningEquipement.show($V(oFormEvenementSSR.equipement_id),'{{$bilan->sejour_id}}');
	}
}

addBorderEvent = function(){
  // Classe des evenements à selectionner
  var category_id = $V(oFormEvenementSSR._category_id);
	var element_id  = $V(oFormEvenementSSR._element_id);
	var eventClass = (element_id) ? ".CElementPrescription-"+element_id : ".CCategoryPrescription-"+category_id;
	var planning = $('planning-sejour');
	
	// On ne passe pas en selected les evenements qui possedent la classe tag_cat
	if(element_id){ 
	  var elements_tag = planning.select(".event.elt_selected"+eventClass+":not(.tag_cat)");
		if(planning.select(".event.elt_selected"+eventClass+".selected:not(.tag_cat)").length){
		  elements_tag.invoke("removeClassName", 'selected');
    } else {
	    elements_tag.invoke("addClassName", 'selected');
  	}
  } else {
	  var elements = $('planning-sejour').select(".event.elt_selected"+eventClass);
		if($('planning-sejour').select(".event.elt_selected"+eventClass+".selected").length){
		  elements.invoke("removeClassName", 'selected');
		} else {
		  elements.invoke("addClassName", 'selected');
		}
	}
	
	planning.select(".event.elt_selected:not("+eventClass+")").invoke("removeClassName", 'selected');

	// Selection de tous les elements qui ont la classe spécifiée
	planning.select(".event"+eventClass).invoke("addClassName", 'elt_selected');
	
	// Deselection de tous les elements deja selectionnés qui n'ont pas la bonne classe
	planning.select(".event:not("+eventClass+")").invoke("removeClassName", 'elt_selected');
  
	// Suppression de la classe tag_cat de tous les evenements selectionnés
	planning.select(".event"+eventClass).invoke("removeClassName", 'tag_cat');
  
	// Si la selection a eu lieu suite au choix d'une categorie, ajout d'une classe aux evenements
	if(!element_id){
    planning.select(".event.elt_selected"+eventClass).invoke("addClassName", 'tag_cat');
  }
	
	// Mise à jour du compteur
	window["planning-"+$('planning-sejour').down('div.planning').id].updateNbSelectEvents();
}

updateCdarrCount = function(line_id, type_cdarr){
  var countCdarr = ($('cdarrs-'+line_id+'-'+type_cdarr).select('input:checked')).length;  
  if(countCdarr){
    $('count-'+line_id+'-'+type_cdarr).update('('+countCdarr+')');
	} else {
    $('count-'+line_id+'-'+type_cdarr).update('');
	}
}

updateModalCdarr = function(){
  var oFormEvents = getForm("form_list_cdarr");
	var url = new Url("ssr", "ajax_update_modal_evts_modif");
	url.addParam("token_evts", $V(oFormEvents.token_evts));
	url.requestUpdate("modal-cdarr", { onComplete: function(){
    if(!$("modal-cdarr").visible()){
	    modalWindow = modal($('modal-cdarr'), {
	      className: 'modal'
	    });
		}
	} })
}

onchangeSeance = function(seance_id){
  if(seance_id){
	  $('date-evenements').hide();
	} else {
	  $('date-evenements').show();	
	}
}

toggleAllDays = function(){
  var toCheck = $V(oFormEvenementSSR.elements._select_week); 
  var days = oFormEvenementSSR.select('input[name^="_days["]');
  days.each(function(e){
    e.checked = toCheck;
  });
}

removeWeek = function(){
  var days = oFormEvenementSSR.select('input[name^="_days["]');
  $V(oFormEvenementSSR.elements._select_week, days.all(function(elem){return elem.checked}));
}

var oFormEvenementSSR;
Main.add(function(){
  oFormEvenementSSR = getForm("editEvenementSSR");
	
	if($('code_auto_complete')){
    var url = new Url("ssr", "httpreq_do_activite_autocomplete");
    url.autoComplete("editEvenementSSR_code", "code_auto_complete", {
      minChars: 2,
      select: "value",
      updateElement: updateFieldCode
    } );
  }
	
	// Initialisation du timePicker
  Calendar.regField(oFormEvenementSSR._heure, null, { minInterval: 10, exactMinutes: false});
	Control.Tabs.create('tabs-activites', true);
	
	{{if $selected_cat}}
	  selectActivite('{{$selected_cat->_guid}}');
    $("technicien-{{$selected_cat->_id}}-{{$current_user_id}}").onclick();
	{{/if}}
});
									
</script>
{{/if}}


{{if !$bilan->technicien_id}} 
<div class="small-warning">
  Le patient n'a pas de 
  {{mb_label object=$bilan field=technicien_id}}
	<a class="button search" href="?&m={{$m}}&amp;tab=vw_idx_repartition">
	  Me rendre à la répartition des patients
	</a>
</div>
{{else}}


<ul id="tabs-activites" class="control_tabs">
  <li>
    <a href="#add_ssr">Boîte à activités</a>
  </li>
  <li>
    <a href="#outils">Outils</a>
  </li>
</ul>
<hr class="control_tabs" />

<div id="add_ssr" style="display: none;">
  <!-- Modification du bilan SSR, brancardage -->
  <form name="editBilanSSR" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
  	<input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="bilan_id" value="{{$bilan->_id}}">
	  <table class="form">
	  	<tr>
			  <th style="width: 94px">{{mb_label object=$bilan field=brancardage}}</th>
			  <td>{{mb_field object=$bilan field=brancardage onchange="this.form.onsubmit();"}}</td>
	    </tr> 
	  </table>
	</form>
	
	<form name="editEvenementSSR" method="post" action="?" onsubmit="return submitSSR();">
	  <input type="hidden" name="m" value="ssr" />
	  <input type="hidden" name="dosql" value="do_evenement_ssr_multi_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="sejour_id" value="{{$bilan->sejour_id}}">
	  
	  {{mb_field hidden=true object=$evenement_ssr field=equipement_id}}
	  {{mb_field hidden=true object=$evenement_ssr field=therapeute_id prop="ref notNull"}}
	  <input type="hidden" name="line_id" value="" />
	  <input type="hidden" name="_element_id" value="" />
	  <input type="hidden" name="_category_id" value="" />
    
	  <table class="form">
	  	<tr>
	  	  <th style="width: 94px">{{mb_label object=$bilan field=entree}}</th>
				<td>{{mb_value object=$bilan field=entree}}</td>
			</tr>
	    <tr>
	      <th>{{mb_label object=$bilan field=technicien_id}}</th>
	      <td><strong>{{mb_value object=$bilan field=technicien_id}}</strong></td>
	    </tr>
	    <tr>
	      <th>Catégories</th>
	      <td class="text">
	        {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line name=category}}
	              {{if $smarty.foreach.category.first}}
	                {{assign var=category value=$_line->_ref_element_prescription->_ref_category_prescription}}
	                <button id="trigger-{{$category->_guid}}" class="none activite" type="button" 
									        onclick="$V(this.form._category_id, '{{$category->_id}}'); selectActivite('{{$category->_guid}}')">
	                  {{$category}}
	                </button>
	              {{/if}}
	            {{/foreach}}
	          {{/foreach}}
	        {{/foreach}}
	      </td>
	    </tr>
	    <tr>
	      <th>Eléments</th>
	      <td class="text">
	        {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line name=category}}
	              {{assign var=element value=$_line->_ref_element_prescription}}
	              {{if $smarty.foreach.category.first}}
	                {{assign var=category value=$element->_ref_category_prescription}}
	                <div class="activite" id="activite-{{$category->_guid}}" style="display: none;">
	              {{/if}}
	              
	              <span style="float: right">
                  {{mb_include module=system template=inc_opened_interval_date from=$_line->debut to=$_line->date_arret}}
	              </span>
                
	              <label>
  	              <input type="radio" name="prescription_line_element_id" id="line-{{$_line->_id}}" class="search line" 
                         onclick="$V(this.form._element_id, '{{$_line->element_prescription_id}}'); selectElement('{{$_line->_id}}'); $V(this.form._cdarr, false); $$('#other_cdarr span').invoke('remove'); $('other_cdarr').hide();" />
  	              <span class="mediuser" style="border-left-color: #{{$element->_color}};" 
                        onmouseover="ObjectTooltip.createEx(this, '{{$element->_guid}}')">
                    {{$_line}}
                  </span>
                </label>
								{{if $_line->commentaire}} 
                <div class="warning text">
                  {{$_line->commentaire}}
                </div>
								{{else}}
                <br />
								{{/if}}

	              {{if $smarty.foreach.category.last}}
	                </div>
	              {{/if}}
	            {{/foreach}}
	          {{/foreach}}
	        {{/foreach}}
	      </td>
	    </tr>
	    <tr id='tr-cdarrs'>
	      <th>Codes CdARR</th>
	      <td class="text">
	      	<button type="button" class="add" onclick="$('remarque_ssr').toggle(); this.form.remarque.focus();" style="float: right">Remarque</button>
	        {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line}}
	              <div class="type-cdarrs" id="type-cdarrs-{{$_line->_id}}" style="display : none;">
	                {{foreach from=$_line->_ref_element_prescription->_ref_cdarrs_by_type key=type_cdarr item=_cdarrs}}
										<!-- Boutons de type de code cdarr-->
										<button class="button-type-cdarrs none" type="button" onclick="selectTypeCdarr('{{$type_cdarr}}','{{$_line->_id}}',this);">
											{{$type_cdarr}} <span class="counts_cdarr" id="count-{{$_line->_id}}-{{$type_cdarr}}"></span>
										</button>
	                {{/foreach}}
	              </div>
	            {{/foreach}}
	          {{/foreach}}
	        {{/foreach}}  
					{{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
            {{foreach from=$_lines_by_chap item=_lines_by_cat}}
              {{foreach from=$_lines_by_cat.element item=_line}}

                <div id="cdarrs-{{$_line->_id}}" style="display : none;">
                  {{foreach from=$_line->_ref_element_prescription->_ref_cdarrs_by_type key=type_cdarr item=_cdarrs}}
                  
                  <!-- Affichage des codes cdarrs -->
                  <div class="cdarrs" id="cdarrs-{{$_line->_id}}-{{$type_cdarr}}" style="display: none;">
                  {{foreach from=$_cdarrs item=_cdarr}}
                    <label>
                      <input type="checkbox" name="cdarrs[{{$_cdarr->code}}]" value="{{$_cdarr->code}}" onclick="updateCdarrCount('{{$_line->_id}}','{{$type_cdarr}}');" /> 
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$_cdarr->_guid}}')">
                      {{$_cdarr->code}}
                      </span>
                    </label>
                    {{/foreach}}
                    </div>
                    
                  {{/foreach}}
                </div>
                
              {{/foreach}}
            {{/foreach}}
          {{/foreach}}  
	         
	        <div id="div_other_cdarr" style="display: none;">
            <label>
	            <input type="checkbox" name="_cdarr" value="other" onclick="toggleOther(this);" /> Autre:
            </label>
            <br />
	          <span id="other_cdarr" style="display: none;">
						   <input type="text" name="code" class="autocomplete" canNull=true size="2" />
	             <div style="display:none;" class="autocomplete" id="code_auto_complete"></div>
	          </span>
	        </div>
	      </td>
	    </tr> 
			<tr id="remarque_ssr" style="display: none;">
        <th>Remarque</th>
        <td>{{mb_field object=$evenement_ssr field=remarque}}</td>
      </tr>
	    <tr>
	      <th>Rééducateur</th>
	      <td class="text">
	      	{{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line name=foreach_category}}
	              {{assign var=element value=$_line->_ref_element_prescription}}
	              
								{{if $smarty.foreach.foreach_category.first}}
								  {{assign var=category value=$element->_ref_category_prescription}}
                  {{assign var=category_id value=$category->_id}}
								  
								   <div class="techniciens" id="techniciens-{{$category->_guid}}" style="display: none;">
									   {{if array_key_exists($category_id, $executants)}}
										   {{assign var=list_executants value=$executants.$category_id}}
	                     {{if array_key_exists($current_user_id, $list_executants)}}
											 
											 {{assign var=current_user value=$list_executants.$current_user_id}}
											 <button title="{{$current_user->_view}}" id="technicien-{{$category_id}}-{{$current_user_id}}" class="none ressource" type="button" onclick="selectTechnicien('{{$current_user->_id}}', this)">
	                       {{$current_user->_user_last_name}}
	                     </button>		 
										 {{/if}}
											 
										 <select class="_technicien_id" onchange="selectTechnicien(this.value)">
										 	  <option value="">&mdash; Rééducateur</option>
	                      {{foreach from=$executants.$category_id item=_user_executant}}
	                        <option value="{{$_user_executant->_id}}">
	                          {{$_user_executant->_user_last_name}}
	                        </option>
	                      {{/foreach}}
	                    </select>
	
										{{else}}
											<div class="small-warning">
		                    Aucun exécutant n'est disponible pour cette catégorie
		                  </div>
		                {{/if}}
									</div>
								{{/if}}
								
							{{/foreach}}
						{{/foreach}}
          {{/foreach}}		
	      </td>
	    </tr>
	    <tr>
	      <th>Equipement</th>
	      <td class="text">
	        {{foreach from=$plateau->_ref_equipements item=_equipement}}
	        <button id="equipement-{{$_equipement->_id}}" class="none equipement" type="button" onclick="$V(getForm('editEvenementSSR')._equipement_id, ''); selectEquipement('{{$_equipement->_id}}');">
	          {{$_equipement}}
	        </button>
	        {{/foreach}}
	        <button id="equipement-" type="button" class="cancel equipement" onclick="$V(getForm('editEvenementSSR')._equipement_id, ''); selectEquipement(''); ">Aucun</button>
					
					<select name="_equipement_id" onchange="selectEquipement(this.value);" style="width: 6em;">
            <option value="">&mdash; {{tr}}Other{{/tr}}</option>
            {{foreach from=$plateaux item=_plateau}}
						  {{if $_plateau->_id != $plateau->_id}}
	              <optgroup label="{{$_plateau->_view}}">
	              {{foreach from=$_plateau->_ref_equipements item=_equipement}}
	                <option value="{{$_equipement->_id}}">{{$_equipement->_view}}</option>
	              {{/foreach}}
	              </optgroup>
							{{/if}}
            {{/foreach}}
          </select>
					
	      </td>
	    </tr>
			<tr id="seances" style="display: none;">
        <th>{{mb_label object=$evenement_ssr field="seance_collective_id"}}</th>
        <td>
				  <table class="layout">
					  <tr>
						  <td>
							  <input type="checkbox" name="seance_collective" value="true" onclick="getForm(editEvenementSSR).seance_collective_id.toggle(); "/>
       	      </td>
					   	<td id="select-seances"></td>
					  </tr>
					</table>
        </td>
      </tr>
			<tbody id="date-evenements">
		    <tr>
		      <th style="vertical-align: middle;">Jour</th>
		      <td style="text-align: center;">
		        <table>
		          <tr>
		            {{foreach from=$list_days key=_date item=_day}}
		              <td>
		                <label>{{$_day}}<br /><input class="days" type="checkbox" onclick="removeWeek();" name="_days[{{$_date}}]" value="{{$_date}}" />
		                </label>
		              </td>
		            {{/foreach}}
                <td style="padding-left: 3em; text-align: center;">
                  <label style="float: right;">
                    {{tr}}Week{{/tr}}<br/>
                    <input type="checkbox" name="_select_week" onclick="toggleAllDays();"/>
                  </label>
                </td>
		          </tr>
		        </table>
		      </td>
		    </tr>	
		    <tr>
		      <th>Heure / Durée (min)</th>
		      <td>
						{{mb_field object=$evenement_ssr field="_heure" form="editEvenementSSR"}}
		        {{mb_field object=$evenement_ssr field="duree" form="editEvenementSSR" increment=1 size=2 step=10}}
					</td>
		    </tr>
			</tbody>
	    <tr>
	      <td colspan="2" class="button">
	        <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
	      </td>
	    </tr>
	  </table>
	</form>
</div>

<div id="outils" style="display: none;">
  
	<script type="text/javascript">
		
		updateSelectedEvents = function(input_elements){
		  $V(input_elements, '');
		  var tab_selected = new TokenField(input_elements); 
		  $$(".event.selected").each(function(e){
		    if(e.className.match(/CEvenementSSR-([0-9]+)/)){
		     var evt_id = e.className.match(/CEvenementSSR-([0-9]+)/)[1];
		     tab_selected.add(evt_id);
		    }
		  });
		}
		
		resetFormSSR = function(){
		  var oForm = getForm('editSelectedEvent');
		  $V(oForm.del, '0');
      $V(oForm._nb_decalage_min_debut, '');
      $V(oForm._nb_decalage_heure_debut, '');
      $V(oForm._nb_decalage_jour_debut, '');
      $V(oForm._nb_decalage_duree, ''); 
			$V(oForm.kine_id, ''); 
			$V(oForm.equipement_id, ''); 
		}
    
    toggleOther = function(elem, dontFocus) {
      var toggle = $V(elem);
      
      $('other_cdarr').setVisible(toggle); 
      $V(elem.form.code, '');
      $(elem.form.code).tryFocus();
      $('other_cdarr').select('input[type=hidden]').each(function(e){e.disabled = toggle ? false : 'disabled';}, elem);
    }

    updateFieldCode = function(selected, input) {
      var code_selected = selected.childElements()[0];
      $('other_cdarr').insert({bottom: 
        DOM.span({}, 
          DOM.input({
            type: 'hidden', 
            id: 'editEvenementSSR__cdarrs['+code_selected.innerHTML+']', 
            name:'_cdarrs['+code_selected.innerHTML+']',
            value: code_selected.innerHTML
          }),
          DOM.button({
            className: "cancel notext", 
            type: "button",
            onclick: "deleteCode(this)"
          }),
          DOM.label({}, code_selected.innerHTML)
        )
      });
         
      var input = $('editEvenementSSR_code');
      input.value = '';
      input.tryFocus();
    }

    deleteCode = function(elem) {
      $(elem).up().remove();
    }
    
	</script>	

	<form name="editSelectedEvent" method="post" action="?" onsubmit="updateSelectedEvents(this.token_elts); 
                                                                    return onSubmitFormAjax(this, { onComplete: function(){ 
																																		    refreshPlanningsSSR(); resetFormSSR(); } } )">
		<input type="hidden" name="m" value="ssr" />
		<input type="hidden" name="dosql" value="do_modify_evenements_aed" />
		<input type="hidden" name="token_elts" value="" />
    <input type="hidden" name="del" value="0" />		
		<input type="hidden" name="sejour_id" value="{{$bilan->sejour_id}}">
    <table class="form">
		 <tr>
        <th class="category" colspan="2">
          Modification des événements sélectionnés
        </th>
      </tr>
			<tr>
				<td>
			    Déplacer de {{mb_field object=$evenement_ssr field="_nb_decalage_min_debut" form="editSelectedEvent" increment=1 size=2 step=10}} minutes
      	</td>
				<td>
					Transférer vers 
					<select name="kine_id">
						<option value="">&mdash; Rééducateur</option>
						{{foreach from=$plateaux item=_plateau}}
						  <optgroup label="{{$_plateau->_view}}">
						  {{foreach from=$_plateau->_ref_techniciens item=_technicien}}
                <option value="{{$_technicien->_ref_kine->_id}}">{{$_technicien->_ref_kine->_view}}</option>
              {{/foreach}}
							</optgroup>
						{{/foreach}}
					</select>
				</td>
			</tr>
      <tr>
        <td>
          Déplacer de {{mb_field object=$evenement_ssr field="_nb_decalage_heure_debut" form="editSelectedEvent" increment=1 size=2}} heures
        </td>
				<td>
					Transférer vers 
				  <select name="equipement_id">
				  	<option value="">&mdash; Equipement</option>
            <option value="none">{{tr}}CEquipement.none{{/tr}}</option>
						{{foreach from=$plateaux item=_plateau}}
              <optgroup label="{{$_plateau->_view}}">
	            {{foreach from=$_plateau->_ref_equipements item=_equipement}}
	              <option value="{{$_equipement->_id}}">{{$_equipement->_view}}</option>
	            {{/foreach}}
							</optgroup>
						{{/foreach}}
          </select>
				</td>
      </tr>	    		
      <tr>
        <td>
          Déplacer de {{mb_field object=$evenement_ssr field="_nb_decalage_jour_debut" form="editSelectedEvent" increment=1 size=2}} jours
        </td>
				<td></td>
      </tr>
			<tr>
				<td>				 
					Modifier la durée de {{mb_field object=$evenement_ssr field="_nb_decalage_duree" form="editSelectedEvent" increment=1 size=2 step=10}} minutes
			  </td>
				<td></td>
			</tr>
			<tr>
				<td class="button" colspan="2">
					<button type="button" onclick="this.form.onsubmit();" class="submit">{{tr}}Modify{{/tr}}</button>
				</td>
			</tr>	
			<tr>
        <th class="category" colspan="2">
          Suppression des événements sélectionnés
        </th>
      </tr>
      <tr>
        <td class="button" colspan="2">
          <button type="button" class="trash" onclick="$V(this.form.del, '1'); this.form.onsubmit();">
            Supprimer
          </button>
        </td>
      </tr>
    </table>
	</form>
	
	<form name="duplicateSelectedEvent" method="post" action="?" onsubmit="updateSelectedEvents(this.token_elts); 
                                                                    return onSubmitFormAjax(this, { onComplete: function(){ 
                                                                        refreshPlanningsSSR(); resetFormSSR(); } } )">
    <input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="dosql" value="do_duplicate_evenements_aed" />
    <input type="hidden" name="token_elts" value="" /> 
    <table class="form">
    	<tr>
        <th class="category">
          Duplication des événements sélectionnés vers la semaine suivante
        </th>
      </tr>
      <tr>
        <td class="button">
          <button type="button" class="submit" onclick="$V(this.form.duplicate, '1'); this.form.onsubmit();">Dupliquer</button>
        </td>
      </tr> 
	  </table>
	</form>
	
	<!-- TODO: utiliser le meme formulaire pour stocker le token d'evenements pour les differentes actions  -->
	<form name="form_list_cdarr">
		<input type="hidden" name="token_evts" />
	</form>	
	
	<!-- Modal de modification des actes cdarrs -->
	<div id="modal-cdarr" style="display: none;"></div>
	
	<table class="form">
		<tr>
			<th class="category">Modification des codes CdARR</td>
		</tr>
		<tr>
			<td class="button">
			  <button type="button" class="submit" onclick="updateSelectedEvents(getForm('form_list_cdarr').token_evts); updateModalCdarr();">Modifier les codes CdARR</button>
      </td>
		</tr>
	</table>

</div>
{{/if}}