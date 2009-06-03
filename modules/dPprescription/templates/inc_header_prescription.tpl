{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

// refresh de la liste des protocoles dans le cas des protocoles
refreshListProtocole = function(oForm){
  var oFormFilter = document.selPrat;
  oFormFilter.praticien_id.value = oForm.praticien_id.value;
  oFormFilter.function_id.value = oForm.function_id.value;
  oFormFilter.group_id.value = oForm.group_id.value;
  if(oFormFilter.praticien_id.value || oFormFilter.function_id.value || oFormFilter.group_id.value){
	  submitFormAjax(oForm, 'systemMsg', { 
	    onComplete : function() { 
	       Protocole.refreshList(oForm.prescription_id.value) 
	    } 
	  });
  }
}

// refresh du select de protocole en fonction du praticien selectionne
refreshSelectProtocoles = function(praticien_id, prescription_id){
	if($('select_protocole')){
	  var url = new Url;
	  url.setModuleAction("dPprescription", "httpreq_vw_select_protocole");
	  url.addParam("praticien_id", praticien_id);
	  url.addParam("prescription_id", prescription_id);
	  url.requestUpdate("select_protocole", { waitingText: null } );
	}
}


changePraticien = function(praticien_id){
  var oFormAddLine = document.addLine;
  var oFormAddLineCommentMed = document.addLineCommentMed;
  var oFormAddLineElement = document.addLineElement;
  
  oFormAddLine.praticien_id.value = praticien_id;
  oFormAddLineCommentMed.praticien_id.value = praticien_id;
  oFormAddLineElement.praticien_id.value = praticien_id;
}

// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
	  changePraticien(document.selPraticienLine.praticien_id.value);
  }
  initPuces();
  if(document.selPraticienLine){
    refreshSelectProtocoles(document.selPraticienLine.praticien_id.value, '{{$prescription->_id}}');
  } else {
    refreshSelectProtocoles('{{$prescription->_ref_current_praticien->_id}}', '{{$prescription->_id}}');
  }
} );

submitProtocole = function(){
  var oForm = document.forms.applyProtocole;
  if(oForm.debut_date){
	  var debut_date = oForm.debut_date.value;
	  if(debut_date != "other" && oForm.debut){
	    oForm.debut.value = debut_date;
	  }
  }
	if(document.selPraticienLine){
   oForm.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  submitFormAjax(oForm, 'systemMsg');
  //oForm.pack_protocole_id.value = '';
  //$V(oForm.debut, '');
}

popupTransmission = function(sejour_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.addParam("addTrans", true);
  url.addParam("with_filter", '0');
  url.popup(700, 500, "Transmissions et Observations");
}

popupDossierMedPatient = function(patient_id, sejour_id, prescription_sejour_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_dossier_medical_patient");
  url.addParam("patient_id", patient_id);
  url.addParam("sejour_id", sejour_id);
  url.addParam("prescription_sejour_id", prescription_sejour_id);
  url.popup(700, 500, "Traitements du patient");
}

</script>

{{assign var=praticien value=$prescription->_ref_praticien}}

{{if $mode_protocole}}
<form name="addLibelle-{{$prescription->_id}}" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />

	<table class="form">
	  <tr>
	    <th class="title" colspan="2">
		    <span style="float: right">
		    	<button type="button" class="submit" onclick="Protocole.duplicate('{{$prescription->_id}}')">Dupliquer</button> 
		      <button type="button" class="search" onclick="Protocole.preview('{{$prescription->_id}}')">Visualiser</button>
		    </span>
		    Modification du protocole
		  </th>
	  </tr>
	  <tr>
	    <th>{{mb_title object=$prescription field=libelle}}</th>
	    <td>
        <input type="text" name="libelle" value="{{$prescription->libelle}}" onchange="refreshListProtocole(this.form);" />
        <button class="tick notext" type="button"></button>
	    </td>
	  </tr>
	   <tr>
	    <th>{{mb_title object=$prescription field=libelle}}</th>
	     <td>
         <!-- Modification du pratcien_id / user_id -->
         <select name="praticien_id" onchange="this.form.function_id.value=''; this.form.group_id.value=''; refreshListProtocole(this.form)">
           <option value="">&mdash; Choix d'un praticien</option>
 	        {{foreach from=$praticiens item=praticien}}
 	        <option class="mediuser" 
 	                style="border-color: #{{$praticien->_ref_function->color}};" 
 	                value="{{$praticien->_id}}"
 	                {{if $praticien->_id == $prescription->praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
 	        </option>
 	        {{/foreach}}
 	      </select>
 	      <select name="function_id" onchange="this.form.praticien_id.value='';this.form.group_id.value=''; refreshListProtocole(this.form)">
           <option value="">&mdash; Choix du cabinet</option>
           {{foreach from=$functions item=_function}}
           <option class="mediuser" style="border-color: #{{$_function->color}}" value="{{$_function->_id}}" 
           {{if $_function->_id == $prescription->function_id}}selected=selected{{/if}}>{{$_function->_view}}</option>
           {{/foreach}}
         </select>
         <select name="group_id" onchange="this.form.praticien_id.value='';this.form.function_id.value=''; refreshListProtocole(this.form)">
           <option value="">&mdash; Choix d'un etablissement</option>
           {{foreach from=$groups item=_group}}
           <option value="{{$_group->_id}}" 
           {{if $_group->_id == $prescription->group_id}}selected=selected{{/if}}>{{$_group->_view}}</option>
           {{/foreach}}
         </select>
	     </td>
		 </tr>
		 {{if $prescription->type != "externe"}}
		 <tr>
			 <th>{{mb_label object=$protocole field="type"}}</th>
			 <td>
		      <select name="type" onchange="refreshListProtocole(this.form);">
		        <option value="pre_admission" {{if $prescription->type == "pre_admission"}}selected="selected"{{/if}}>Pré-admission</option>
		        <option value="sejour" {{if $prescription->type == "sejour"}}selected="selected"{{/if}}>Séjour</option>
		        <option value="sortie" {{if $prescription->type == "sortie"}}selected="selected"{{/if}}>Sortie</option>
		      </select>  
			  </td>
		  </tr>
	   {{/if}}
  </table>
</form>
{{/if}}
      
<form name="moment_unitaire">
  <select name="moment_unitaire_id" style="width: 150px; display: none;">  
    <option value="">&mdash; Sélection du moment</option>
   {{foreach from=$moments key=type_moment item=_moments}}
   <optgroup label="{{$type_moment}}">
   {{foreach from=$_moments item=moment}}
   {{if $type_moment == "Complexes"}}
     <option value="complexe-{{$moment->code_moment_id}}">{{$moment->_view}}</option>
   {{else}}
     <option value="unitaire-{{$moment->_id}}">{{$moment->_view}}</option>
   {{/if}}
   {{/foreach}}
   </optgroup>
   {{/foreach}}
  </select>
</form>
       
<table class="form">
  {{if !$mode_protocole}}
  <tr>
    <th class="title text" colspan="3">
      <!-- Selection du praticien prescripteur de la ligne -->
       <div style="float: right">
       	{{if !$mode_protocole && $prescription->type == "sejour"}}
					<span style="float: right;">
					  <button type="button" class="search" onclick="popupTransmission('{{$prescription->object_id}}');">Transmissions</button>
						<button type="button" class="search" onclick="popupDossierMedPatient('{{$prescription->_ref_patient->_id}}','{{$prescription->object_id}}','{{$prescription->_id}}');">Traitements du patient</button>
					</span>
			  {{/if}}
			  
        {{if !$is_praticien && !$mode_protocole && ($operation_id || $can->admin || $mode_pharma)}}  
        <br />
        Praticien:    
				<form name="selPraticienLine" action="?" method="get">
				  <select name="praticien_id" onchange="changePraticienMed(this.value); {{if !$mode_pharma}}changePraticienElt(this.value);{{/if}} refreshSelectProtocoles(this.value, '{{$prescription->_id}}')">
						<optgroup label="Responsables">
				      <option class="mediuser" style="border-color: #{{$prescription->_ref_current_praticien->_ref_function->color}};" 
						          value="{{$prescription->_ref_current_praticien->_id}}"
						          {{if $prescription->_ref_current_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$prescription->_ref_current_praticien->_view}}</option>
				      {{if @$operation->_ref_anesth->_id}}
				        <option  class="mediuser" style="border-color: #{{$operation->_ref_anesth->_ref_function->color}};" 
				                 value="{{$operation->_ref_anesth->_id}}"
				                 {{if $operation->_ref_anesth->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$operation->_ref_anesth->_view}}</option>
				      {{/if}}
				    </optgroup>
				    <optgroup label="Tous les praticiens">
				      {{foreach from=$listPrats item=_praticien}}
                <option class="mediuser" 
						            style="border-color: #{{$_praticien->_ref_function->color}};" 
						            value="{{$_praticien->_id}}"
						            {{if $_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
						    </option>
              {{/foreach}}
				    </optgroup>
				  </select>
				</form>
				{{/if}}
       </div>
			
      {{if !$mode_protocole && $prescription->object_class == "CSejour"}}
        <div style="float:left; padding-right: 5px; " class="noteDiv {{$prescription->_ref_object->_class_name}}-{{$prescription->_ref_object->_id}};">
          <img alt="Ecrire une note" src="images/icons/note_grey.png" />
        </div>
      {{/if}}
      
      {{if !$mode_protocole}}
        {{if $prescription->type == "externe"}}
         <span style="float: left">
         {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$prescription->_ref_patient size=42}}
         </span>
        {{else}}
         <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$prescription->_ref_patient->_id}}"'>
          {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$prescription->_ref_patient size=42}}
         </a>
       {{/if}}
      {{/if}}
      
      {{if !$mode_protocole}}
        {{if $prescription->type == "externe"}}
          {{$prescription->_ref_patient->_view}}   
        {{else}}
          {{$prescription->_ref_object->_view}}
        {{/if}}
        {{if $prescription->_ref_patient->_age}}
           <br />({{$prescription->_ref_patient->_age}} ans - {{$prescription->_ref_patient->naissance|date_format:"%d/%m/%Y"}}{{if $poids}} - {{$poids}} kg{{/if}})
        {{/if}}
	       <div id="antecedent_allergie">
			     {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
			     {{assign var=sejour_id value=$prescription->_ref_object->_id}}
			     {{include file="../../dPprescription/templates/inc_vw_antecedent_allergie.tpl"}}    
				 </div>   
	    {{/if}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    {{if !$mode_protocole && !$mode_pharma && ($is_praticien || @$operation_id || $can->admin)}}
      <th class="category">Protocoles</th>
    {{/if}}
    {{if !$mode_protocole && ($is_praticien || @$operation_id || $can->admin)}}
      <th class="category">Date d'ajout de lignes</th>
    {{/if}}
    <th class="category">Outils</th>
  </tr>
  <tr>
  {{if !$mode_protocole && !$mode_pharma && ($is_praticien || @$operation_id || $can->admin)}}
   <td class="date" style="text-align: right;">
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?">
	      <table class="form">
	        <tr>
		        <td>
			        <input type="hidden" name="m" value="dPprescription" />
			        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
			        <input type="hidden" name="del" value="0" />
			        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			        <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			        <input type="hidden" name="pratSel_id" value="" />
							<span id="select_protocole"></span>
							<br />
				 				{{if $prescription->type != "externe"}}
				 				  {{if $prescription->_dates_dispo}}
					 				  Intervention
					 				  <select name="operation_id">
					 				    {{foreach from=$prescription->_dates_dispo key=operation_id item=_date_operation}}
					 				      <option value="{{$operation_id}}">{{$_date_operation|date_format:$dPconfig.datetime}}</option>
					 				    {{/foreach}}
	 									</select>
 									{{/if}}
				 				{{else}}
				 				  <!-- Prescription externe -->
									{{mb_field object=$protocole_line field="debut" form=applyProtocole}}       
					 				<script type="text/javascript">
					 				  dates = {
									    current: {
									      start: "{{$today}}",
									      stop: ""
									    }
									  }
					 				  Main.add( function(){
					            Calendar.regField(getForm("applyProtocole").debut, dates);
					          } );
					 				</script>				 				
				 				{{/if}}
			          <button type="button" class="submit" onclick="if(document.selPraticienLine){ $V(this.form.pratSel_id, document.selPraticienLine.praticien_id.value); }submitProtocole(this.form);">Appliquer</button>
		        </td>
	        </tr>
	      </table>
      </form>
    </td>  
  {{/if}}
  
  {{if !$mode_protocole && ($is_praticien || @$operation_id || $can->admin)}}
      <td class="date">
        <form name="selDateLine" action="?" method="get"> 
      
        {{if $prescription->type != "externe"}}   
	        <select name="debut_date" 
					        onchange="$('selDateLine_debut_da').innerHTML = new String;
	 				                    this.form.debut.value = '';
	 				          				  if(this.value == 'other') { $('calendarProt').show(); } 
	 				          				  else { this.form.debut.value = this.value; $('calendarProt').hide();}">
				    <option value="other">Autre date</option>
				    <optgroup label="Séjour">
				      <option value="{{$prescription->_ref_object->_entree|date_format:'%Y-%m-%d'}}">Entrée: {{$prescription->_ref_object->_entree|date_format:"%d/%m/%Y"}}</option>
				      <option value="{{$prescription->_ref_object->_sortie|date_format:'%Y-%m-%d'}}">Sortie: {{$prescription->_ref_object->_sortie|date_format:"%d/%m/%Y"}}</option>
				    </optgroup>
				    <optgroup label="Intervention">
				    {{foreach from=$prescription->_ref_object->_dates_operations item=_date_operation}}
				      <option value="{{$_date_operation}}">{{$_date_operation|date_format:"%d/%m/%Y"}}</option>
				    {{/foreach}}
						</optgroup>
				  </select>		 				
				  <!-- Prescription externe -->
				  <div id="calendarProt" style="border:none; margin-right: 60px">
				    {{mb_field object=$filter_line field="debut" form=selDateLine}}
				    {{mb_field object=$filter_line field="time_debut" form=selDateLine}}      
				  </div>
        {{else}}
           {{mb_field object=$filter_line field="debut" form="selDateLine"}}
        {{/if}}
        
         <script type="text/javascript">
	  	   Main.add( function(){
		       Calendar.regField(getForm("selDateLine").debut);
	    	} );
        </script>	
	    </form>
	    </td>
	  {{/if}} 
 
    <td style="text-align: left;">
      <select name="affichageImpression" onchange="Prescription.popup('{{$prescription->_id}}', this.value); this.value='';" style="width: 65px;">
        <option value="">&mdash; Action</option>
        <optgroup label="Afficher">
      	  <option value="viewAlertes">Alertes</option>
      		{{if $prescription->object_id}}
      		<option value="viewHistorique">Historique</option>
      		<option value="viewSubstitutions">Substitutions</option>
      	  {{/if}}
        </optgroup>
        {{if $prescription->object_id && ($is_praticien || $mode_protocole || @$operation_id || $can->admin) && $prescription->type != "externe"}}
	         <optgroup label="Traitements perso">
	          <option value="stopPerso" onclick="Prescription.stopTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Arrêter</option>
	          <option value="goPerso" onclick="Prescription.goTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Reprendre</option>
	        </optgroup>
        {{/if}}
      </select>

			{{if !$mode_pharma && ($is_praticien || $mode_protocole || @$operation_id || $can->admin)}}
        <button class="new" type="button" onclick="viewEasyMode('{{$mode_protocole}}','{{$mode_pharma}}', menuTabs.activeContainer.id);">Mode grille</button>
        {{if $prescription->object_id}}
          {{if $is_praticien}}
            <form name="removeLines" method="post" action="">
              <input type="hidden" name="dosql" value="do_remove_lines" />
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
              <button type="button" class="trash" onclick="if(confirm('Etes vous sur de vouloir supprimer vos lignes non signées ?')){
                submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reloadPrescSejour('{{$prescription->_id}}',null, null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}}); } } )
              }">Supprimer</button>
             </form>
          {{/if}}
        {{/if}}
      {{/if}}
      <button type="button" class="print notext" onclick="Prescription.printPrescription('{{$prescription->_id}}');"/></button>
      <br />
      {{if $prescription->object_id && ($is_praticien || $mode_protocole || @$operation_id || $can->admin)}}
        {{if !$mode_pharma}}
	        {{if $is_praticien}}
	          <form name="signaturePrescription" method="post" action="">
              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
              <input type="hidden" name="chapitre" value="all" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
              <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg');">Signer toutes les lignes</button>
            </form>
	        {{else}}
		        <!-- Validation de la prescription -->
			      <button type="button" class="tick" onclick="Prescription.valideAllLines('{{$prescription->_id}}');">
			        Tout signer
			      </button>
			      {{if $operation_id}}
              <button type="button" class="cancel" onclick="Prescription.valideAllLines('{{$prescription->_id}}','1')">Annuler signatures</button>
            {{/if}}
		      {{/if}}
        {{/if}}
      {{/if}}
    </td>
  </tr>  
  {{if $praticien_sortie_id && $prescription->_praticiens|@count > 1}}
  <tr>
    <th class="category" style="background-color: #B00; color: #fff" colspan="3">Prescription affichée partiellement :<br />certaines lignes ont été prescrites par d'autres praticiens</th>
  </tr>
  {{/if}}
</table>
<hr />