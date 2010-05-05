{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $bilan->kine_id}} 
<script type="text/javascript">

selectActivite = function(activite) {
  $V(oFormEvenementSSR.prescription_line_element_id, '');
  $V(oFormEvenementSSR._element_id, '');

  $$("button.activite").invoke("removeClassName", "selected");
  $("trigger-"+activite).addClassName("selected");
  $$("div.activite").invoke("hide");
  $("activite-"+activite).show();

  // Mise en evidence des elements dans les plannings
  addBorderEvent();
}

selectTechnicien = function(kine_id) {
  $V(oFormEvenementSSR.therapeute_id, kine_id);	
  $$("button.ressource").invoke("removeClassName", "selected");
  $("technicien-"+kine_id).addClassName("selected");

  PlanningTechnicien.show(kine_id, null, '{{$bilan->sejour_id}}');
	if($V(oFormEvenementSSR.equipement_id)){
	  PlanningEquipement.show($V(oFormEvenementSSR.equipement_id), '{{$bilan->sejour_id}}');
	}
}

selectEquipement = function(equipement_id) {
  $V(oFormEvenementSSR.equipement_id, equipement_id);
	$$("button.equipement").invoke("removeClassName", "selected");
  $("equipement-"+equipement_id).addClassName("selected");
	
	if(equipement_id){
  	PlanningEquipement.show(equipement_id,'{{$bilan->sejour_id}}');
	} else {
	  PlanningEquipement.hide();
  }
}

selectElement = function(line_id){
  $V(oFormEvenementSSR.line_id, line_id);

  $$("button.line").invoke("removeClassName", "selected" );
  $("line-"+line_id).addClassName("selected");
	
	$$("div.cdarrs").invoke("hide");
	$V(getForm("editEvenementSSR").cdarr, '');
	$("cdarrs-"+line_id).show();
	$('div_other_cdarr').show();

  // Mise en evidence des elements dans les plannings
	addBorderEvent();
}

submitSSR = function(){
  if(!$V(oFormEvenementSSR.cdarr) && !$V(oFormEvenementSSR.code)){
	  alert("Veuillez selectionner un code SSR");
		return false;
	}
	if(!$$("button.equipement.selected").length){
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
	}} );
}

refreshPlanningsSSR = function(){
  Planification.refreshSejour('{{$bilan->sejour_id}}');
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
		if(planning.select(".event.elt_selected"+eventClass+":not(.tag_cat).selected").length){
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

var oFormEvenementSSR;
Main.add(function(){
  oFormEvenementSSR = getForm("editEvenementSSR");
	selectTechnicien('{{$bilan->kine_id}}');
	
	if($('code_auto_complete')){
    var url = new Url("ssr", "httpreq_do_activite_autocomplete");
    url.autoComplete("editEvenementSSR_code", "code_auto_complete", {
      minChars: 2,
      select: ".value"
    } );
  }
	
	// Initialisation du timePicker
	Calendar.regField(oFormEvenementSSR._heure, null, { minInterval: 10 });
	
	Main.add(function () {
    Control.Tabs.create('tabs-activites', true);
  });
});
									
</script>
{{/if}}


{{if !$bilan->kine_id}} 
<div class="small-warning">
  Le patient n'a pas de 
  {{mb_label object=$bilan field=kine_id}}
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
	<form name="editEvenementSSR" method="post" action="?" onsubmit="return submitSSR();">
	  <input type="hidden" name="m" value="ssr" />
	  <input type="hidden" name="dosql" value="do_evenement_ssr_multi_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="sejour_id" value="{{$bilan->sejour_id}}">
	  
	  {{mb_field hidden=true object=$evenement_ssr field=equipement_id}}
	  {{mb_field hidden=true object=$evenement_ssr field=therapeute_id}}
	  <input type="hidden" name="line_id" value="" />
	  <input type="hidden" name="_element_id" value="" />
	  <input type="hidden" name="_category_id" value="" />
    
	  <table class="form">
	  	<tr>
	  	  <th>{{mb_label object=$bilan field=entree}}</th>
				<td>{{mb_value object=$bilan field=entree}}</td>
			</tr>
	    <tr>
	      <th>{{mb_label object=$bilan field=kine_id}}</th>
	      <td><strong>{{mb_value object=$bilan field=kine_id}}</strong></td>
	    </tr>
	    <tr>
	      <th>Activités</th>
	      <td>
	        {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line name=category}}
	              {{if $smarty.foreach.category.first}}
	                {{assign var=category value=$_line->_ref_element_prescription->_ref_category_prescription}}
	                <button id="trigger-{{$category->_guid}}" class="search activite" type="button" 
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
	      <td>
	        {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line name=category}}
	              {{assign var=element value=$_line->_ref_element_prescription}}
	              {{if $smarty.foreach.category.first}}
	                {{assign var=category value=$element->_ref_category_prescription}}
	                <div class="activite" id="activite-{{$category->_guid}}" style="display: none;">
	              {{/if}}
	              
	               <span style="float: right">
	              {{if $_line->debut}}
	                à partir du {{mb_value object=$_line field="debut"}}
	              {{/if}}
	              {{if $_line->date_arret}}
	                jusqu'au {{mb_value object=$_line field="date_arret"}}
	              {{/if}}
	              </span>
	              <label>
	              <input type="radio" name="prescription_line_element_id" id="line-{{$_line->_id}}" class="search line" onclick="$V(this.form._element_id, '{{$_line->element_prescription_id}}'); selectElement('{{$_line->_id}}');" />
	              {{$_line->_view}}
	              </label>
	              <br />
	              {{if $smarty.foreach.category.last}}
	                </div>
	              {{/if}}
	            {{/foreach}}
	          {{/foreach}}
	        {{/foreach}}
	      </td>
	    </tr>
	    <tr>
	      <th>Codes CdARR</th>
	      <td>
	        {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	          {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	            {{foreach from=$_lines_by_cat.element item=_line}}
	              <div class="cdarrs" id="cdarrs-{{$_line->_id}}" style="display : none;">
	                {{foreach from=$_line->_ref_element_prescription->_back.cdarrs item=_cdarr}}
	                  <label title="{{$_cdarr->commentaire}}">
	                    <input type="radio" name="cdarr" value="{{$_cdarr->code}}" onclick="$('other_cdarr').hide(); $V(this.form.code, '')" /> {{$_cdarr->code}}
	                  </label>
	                {{/foreach}}
	                
	              </div>
	            {{/foreach}}
	          {{/foreach}}
	        {{/foreach}}  
	         
	        <div id="div_other_cdarr" style="display: none;">
	          <input type="radio" name="cdarr" value="other" onclick="$('other_cdarr').show();" /> Autre
	          <span id="other_cdarr" style="display: none;">
	            {{mb_field object=$evenement_ssr field=code class="autocomplete" canNull=true}}
	             <div style="display:none;" class="autocomplete" id="code_auto_complete"></div>
	          </span>
	        </div>
	      </td>
	    </tr> 
	    <tr>
	      <th>Technicien</th>
	      <td>
	        {{foreach from=$plateau->_ref_techniciens item=_technicien}}
	        <button id="technicien-{{$_technicien->_ref_kine->_id}}" class="search ressource" type="button" onclick="selectTechnicien('{{$_technicien->_ref_kine->_id}}')">
	          {{$_technicien}}
	        </button>
	        {{/foreach}}
	      </td>
	    </tr>
	    <tr>
	      <th>Equipement</th>
	      <td>
	        {{foreach from=$plateau->_ref_equipements item=_equipement}}
	        <button id="equipement-{{$_equipement->_id}}" class="search equipement" type="button" onclick="selectEquipement('{{$_equipement->_id}}');">
	          {{$_equipement}}
	        </button>
	        {{/foreach}}
	        <button id="equipement-" type="button" class="cancel equipement" onclick="selectEquipement('');">Aucun</button>
	      </td>
	    </tr>
	    <tr>
	      <th style="vertical-align: middle;">Jour</th>
	      <td style="text-align: center;">
	        <table>
	          <tr>
	            {{foreach from=$list_days key=_date item=_day}}
	              <td>
	                <label>{{$_day}}<br /><input class="days" type="checkbox" name="_days[{{$_date}}]" value="{{$_date}}" />
	                </label>
	              </td>
	            {{/foreach}}
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
		    var evt_id = e.className.match(/CEvenementSSR-([0-9]+)/)[1];
		    tab_selected.add(evt_id);
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
		
	</script>	

	<form name="editSelectedEvent" method="post" action="?" onsubmit="updateSelectedEvents(this.token_elts); 
                                                                    return onSubmitFormAjax(this, { onComplete: function(){ 
																																		    refreshPlanningsSSR(); resetFormSSR(); } } )">
		<input type="hidden" name="m" value="ssr" />
		<input type="hidden" name="dosql" value="do_modify_evenements_aed" />
		<input type="hidden" name="token_elts" value="" />
    <input type="hidden" name="del" value="0" />		
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
					Transférer vers le planning 
					<select name="kine_id">
						<option value="">&mdash; Choix d'un kiné</option>
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
					Transférer vers le planning 
				  <select name="equipement_id">
				  	<option value="">&mdash; Choix d'un équipement</option>
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
</div>
{{/if}}