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

changePraticien = function(praticien_id){
  var oFormAddLine = document.addLine;
  var oFormAddLineCommentMed = document.addLineCommentMed;
  var oFormAddLineElement = document.addLineElement;
  
  oFormAddLine.praticien_id.value = praticien_id;
  oFormAddLineCommentMed.praticien_id.value = praticien_id;
  oFormAddLineElement.praticien_id.value = praticien_id;
}

submitProtocole = function(){
  var oForm = getForm("applyProtocole");
  if(oForm.debut_date){
	  var debut_date = oForm.debut_date.value;
	  if(debut_date != "other" && oForm.debut){
	    oForm.debut.value = debut_date;
	  }
  }
	if(document.selPraticienLine){
   oForm.praticien_id.value = document.selPraticienLine.praticien_id.value;
	 oForm.pratSel_id.value = document.selPraticienLine.praticien_id.value
  }	
  return onSubmitFormAjax(oForm, 'systemMsg');
}

popupTransmission = function(sejour_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.addParam("addTrans", true);
  url.addParam("with_filter", '0');
  url.popup(700, 500, "Transmissions et Observations");
}

changeManualDate = function(){
  var oForm = getForm('selDateLine'); 
	oForm.debut_da.value = new String;
  oForm.debut.value = '';

  if($V(oForm.debut_date) == 'other') { 
	  $('manualDate').show(); 
		$('relativeDate').hide(); 
	} else {
	  var decalage = $V(oForm.debut_date).split('_');
		var jour_decalage = decalage[0];
	  var dateTime = decalage[1];
		var operation_id;
		
		if(jour_decalage != "I"){
	    $V(oForm.unite_decalage,"jour");
	    oForm.unite_decalage.disabled = "disabled";
	  } else {
	    oForm.unite_decalage.disabled = "";
	  }
		
		if(jour_decalage == "I" && decalage[2]){
		  operation_id = decalage[2];
			$V(oForm.operation_id, operation_id);
		}
		
		$V(oForm.jour_decalage, jour_decalage);
	  
		var dDebut = Date.fromDATETIME(dateTime);  
		if($V(oForm.unite_decalage) == "jour"){
		  dDebut.addDays(parseInt(oForm.decalage_line.value));
      $V(oForm.debut, dDebut.toDATE());
		} else {
		  dDebut.addHours(parseInt(oForm.decalage_line.value));
	    $V(oForm.debut, dDebut.toDATE());
	    $V(oForm.time_debut, dDebut.toTIME());
    }
		$('manualDate').hide();
		$('relativeDate').show();
	}
}

printBons = function(prescription_id){
  var url = new Url("dPprescription", "print_bon");
  url.addParam("prescription_id", prescription_id);
  url.popup(900, 600, "Impression des bons");
}

Main.add( function(){
  var oFormProtocole = getForm("applyProtocole");
  var praticien_id;
  initNotes();
  if(document.selPraticienLine){
    praticien_id = document.selPraticienLine.praticien_id.value;
		{{if $praticien_for_prot_id}}
      document.selPraticienLine.praticien_id.value = {{$praticien_for_prot_id}};   
    {{/if}}
    changePraticien(praticien_id);
  } else {
    praticien_id = '{{$prescription->_ref_current_praticien->_id}}';
  }
  headerPrescriptionTabs = Control.Tabs.create('header_prescription', false);

  if(oFormProtocole){
	  var url = new Url("dPprescription", "httpreq_vw_select_protocole");
	  url.autoComplete(oFormProtocole.libelle_protocole, "protocole_auto_complete", {
		  dropdown: true,
	    minChars: 1,
      select: "view",
      valueElement: oFormProtocole.elements.pack_protocole_id,
	    callback: 
	      function(input, queryString){
				  if(document.selPraticienLine){
	          return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id="+$V(document.selPraticienLine.praticien_id)); 
					} else {
					  return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id="+praticien_id); 
	        }
	      }
	  } );	
  }
} );

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
        <button style="float: left" type="button" class="hslip notext" onclick="$('list_protocoles').toggle();" title="Afficher/cacher la colonne de gauche"></button>
			  <span style="float: right">
		    	<button type="button" class="add" onclick="Protocole.duplicate('{{$prescription->_id}}')">Dupliquer</button> 
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
	    <th>{{mb_title object=$prescription field=_owner}}</th>
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
       {{if $mode_pharma}}
         <button style="float: left" type="button" class="hslip notext" onclick="$('left-column').toggle();" title="Afficher/cacher la colonne de gauche"></button>
        {{/if}}
			 <div style="float: right">
       	{{if !$is_praticien && !$mode_protocole && ($operation_id || $can->admin || $mode_pharma || $current_user->isInfirmiere())}}
				<form name="selPraticienLine" action="?" method="get">
				  <select name="praticien_id" onchange="changePraticienMed(this.value); {{if !$mode_pharma}}changePraticienElt(this.value);{{/if}}">
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
				<br />
				  <div id="antecedent_allergie" style="float: right">
            {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
            {{assign var=sejour_id value=$prescription->_ref_object->_id}}
            {{include file="../../dPprescription/templates/inc_vw_antecedent_allergie.tpl"}}    
          </div>   
        </div>
      {{if !$mode_protocole && $prescription->object_class == "CSejour"}}
        <div style="float:left; padding-right: 5px;" class="noteDiv {{$prescription->_ref_object->_guid}}">
          <img title="Ecrire une note" src="images/icons/note_grey.png" />
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
           <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_ref_object->_guid}}')">{{$prescription->_ref_object->_view}}</a>
        {{/if}}
        {{if $prescription->_ref_patient->_age}}
           <br />({{$prescription->_ref_patient->_age}} ans - {{$prescription->_ref_patient->naissance|date_format:"%d/%m/%Y"}}{{if $poids}} - {{$poids}} kg{{/if}})
        {{/if}}
	    {{/if}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
  	<td colspan="3">
			<ul id="header_prescription" class="control_tabs small">
				{{if !$mode_protocole && !$mode_pharma && ($is_praticien || @$operation_id || $can->admin || $current_user->isInfirmiere())}}
				<li><a href="#div_protocoles">Protocoles</a></li>
				{{/if}}
				{{if !$mode_protocole && ($is_praticien || @$operation_id || $can->admin || $current_user->isInfirmiere())}}
				<li><a href="#div_ajout_lignes">Paramètres d'ajout de lignes</a></li>
				{{/if}}
				<li><a href="#div_outils">Outils</a></li>
				<li style="float: right; button">		
					{{if $prescription->object_id && ($is_praticien || $mode_protocole || @$operation_id || $can->admin || $current_user->_is_infirmiere)}}
		        {{if !$mode_pharma}}
		          {{if $is_praticien}}
		            <form name="signaturePrescription" method="post" action="">
		              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
		              <input type="hidden" name="m" value="dPprescription" />
		              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		              <input type="hidden" name="chapitre" value="all" />
		              <input type="hidden" name="del" value="0" />
		              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
		              <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg');" style="margin:0px">Tout signer</button>
		            </form>
		          
		            <form name="removeSignaturePrescription" method="post" action="">
		              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
		              <input type="hidden" name="m" value="dPprescription" />
		              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		              <input type="hidden" name="chapitre" value="all" />
		              <input type="hidden" name="annulation" value="1" />
		              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
		              <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg');" style="margin:0px">Annuler signatures</button>
		            </form>
								
							  <form name="removeLines" method="post" action="">
	                <input type="hidden" name="dosql" value="do_remove_lines" />
	                <input type="hidden" name="m" value="dPprescription" />
	                <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	                <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	                <button type="button" class="trash" style="margin:0px" onclick="if(confirm('Etes vous sur de vouloir supprimer vos lignes non signées ?')){
	                  submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reloadPrescSejour('{{$prescription->_id}}',null, null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}}); } } )
	                }">Supprimer</button>
               </form>
		          {{else}}
		            <!-- Validation de la prescription -->
		            <button type="button" class="tick" onclick="Prescription.valideAllLines('{{$prescription->_id}}');" style="margin:0px">
		              Tout signer
		            </button>
		            <button type="button" class="cancel" onclick="Prescription.valideAllLines('{{$prescription->_id}}','1')" style="margin:0px">
		            	Annuler signatures
								</button>
								
								{{if $current_user->_is_infirmiere}}
									<form name="removeLines" method="post" action="">
		                  <input type="hidden" name="dosql" value="do_remove_lines" />
		                  <input type="hidden" name="m" value="dPprescription" />
		                  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		                  <input type="hidden" name="praticien_id" value="" />
		                  <button type="button" class="trash" style="margin:0px" onclick="$V(this.form.praticien_id, $V(getForm('selPraticienLine').praticien_id)); if(confirm('Etes vous sur de vouloir supprimer les lignes non signées du praticien selectionné ?')){
		                    submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reloadPrescSejour('{{$prescription->_id}}',null, null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}}); } } )
		                  }">Supprimer</button>
		               </form>
								 {{/if}}
		          {{/if}}
		        {{/if}}
		      {{/if}}			
				</li>
			</ul>
			<hr class="control_tabs" />
		</td>
  </tr>
  <tr>
  {{if !$mode_protocole && !$mode_pharma && ($is_praticien || @$operation_id || $can->admin || $current_user->isInfirmiere())}}
   <td id="div_protocoles" colspan="3">
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?" onsubmit="return submitProtocole();">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	      <input type="hidden" name="pratSel_id" value="" />
        
	      <input type="hidden" name="pack_protocole_id" value="" />
	      <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" size="20" class="autocomplete" />
	      <div style="display:none; width: 350px;" class="autocomplete" id="protocole_auto_complete"></div>
	
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
        <button type="button" class="submit" onclick="this.form.onsubmit();">Appliquer</button>
      </form>
    </td>  
  {{/if}}
  
  {{if !$mode_protocole && ($is_praticien || @$operation_id || $can->admin || $current_user->isInfirmiere())}}
      <td id="div_ajout_lignes" colspan="3" style="display: none;">
		    <form name="selDateLine" action="?" method="get"> 
        {{if $prescription->type != "externe"}} 
	        <select name="debut_date" onchange="changeManualDate();">
				    <option value="other">Autre date</option>
            <optgroup label="Intervention">
            {{foreach from=$prescription->_dates_dispo key=_operation_id item=_date_operation}}
              <option value="I_{{$_date_operation}}_{{$_operation_id}}">Intervention - {{$_date_operation|date_format:$dPconfig.datetime}}</option>
            {{/foreach}}
            </optgroup>
				    <optgroup label="Séjour">
				      <option value="E_{{$prescription->_ref_object->_entree}}">Entrée - {{$prescription->_ref_object->_entree|date_format:$dPconfig.datetime}}</option>
				      <option value="S_{{$prescription->_ref_object->_sortie}}">Sortie - {{$prescription->_ref_object->_sortie|date_format:$dPconfig.datetime}}</option>
				    </optgroup>
				  </select>		 				
					<!-- Selection manuelle de la date -->
				  <span id="manualDate" style="border:none; margin-right: 60px">
				    {{mb_field object=$filter_line field="debut" form=selDateLine}}
				    {{mb_field object=$filter_line field="time_debut" form=selDateLine}}
				  </span>
					<!-- Selection relative de la date -->
					<span id='relativeDate' style="border:none; margin-right: 60px; display: none;">
					  {{mb_field object=$filter_line field="decalage_line" form=selDateLine showPlus=1 increment=1 size="3" onchange="changeManualDate()"}}
						{{mb_field object=$filter_line field="unite_decalage" onchange="changeManualDate()"}}
					</span>
					<input type="hidden" name="jour_decalage" value="" />
					<input type="hidden" name="operation_id" value="" />
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
		
    <td colspan="3" id="div_outils" style="display: none;">
      <select name="affichageImpression" onchange="Prescription.popup('{{$prescription->_id}}', this.value); this.value='';">
        <option value="">&mdash; Action</option>
        <optgroup label="Afficher">
      	  <option value="viewAlertes">Alertes</option>
      		{{if $prescription->object_id}}
      		<option value="viewHistorique">Historique</option>
      		<option value="viewSubstitutions">Substitutions</option>
					{{if !$mode_protocole && $prescription->type == "sejour"}}
          <option value="traitement">Traitements du patient</option>
          {{/if}}
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
      {{/if}}
			{{if !$mode_protocole && $prescription->type == "sejour"}}
        <button type="button" class="search" onclick="popupTransmission('{{$prescription->object_id}}');">Transmissions</button>
			{{/if}}			
			<button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}');" />Ordonnance</button>
      <button type="button" class="print" onclick="printBons('{{$prescription->_id}}');" title="{{tr}}Print{{/tr}}">Bons</button>
    </td>
  </tr>  
  {{if $praticien_sortie_id && $prescription->_praticiens|@count > 1}}
  <tr>
    <th class="category" style="background-color: #B00; color: #fff" colspan="3">Prescription affichée partiellement :<br />certaines lignes ont été prescrites par d'autres praticiens</th>
  </tr>
  {{/if}}
</table>
<hr class="control_tabs" />
