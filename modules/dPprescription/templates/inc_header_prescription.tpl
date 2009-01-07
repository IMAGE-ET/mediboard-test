<script type="text/javascript">

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

// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
	  changePraticien(document.selPraticienLine.praticien_id.value);
  }
  initPuces();
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

</script>

{{assign var=praticien value=$prescription->_ref_praticien}}

<table class="form">
  <tr>
    <th class="title text" colspan="3">
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

      <!-- Selection du praticien prescripteur de la ligne -->
      {{*if !$is_praticien && !$mode_protocole && $prescription->_can_add_line*}}
			{{if !$is_praticien && !$mode_protocole}}
       <div style="float: right">
				<form name="selPraticienLine" action="?" method="get">
				  <select name="praticien_id" onchange="changePraticienMed(this.value); {{if !$mode_pharma}}changePraticienElt(this.value);{{/if}}">
				    {{foreach from=$listPrats item=_praticien}}
					    <option class="mediuser" 
					            style="border-color: #{{$_praticien->_ref_function->color}};" 
					            value="{{$_praticien->_id}}"
					            {{if $_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
					    </option>
				    {{/foreach}}
				  </select>
				</form>
       </div>
			{{/if}}
			 
      {{if !$mode_protocole && $prescription->object_class == "CSejour"}}
        <div style="float:left; padding-right: 5px; " class="noteDiv {{$prescription->_ref_object->_class_name}}-{{$prescription->_ref_object->_id}};">
          <img alt="Ecrire une note" src="images/icons/note_grey.png" />
        </div>
      {{/if}}
     
      {{if $mode_protocole}}
        <!-- Formulaire de modification du libelle de la prescription -->
        <form name="addLibelle-{{$prescription->_id}}" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
          Libelle: 
          <input type="text" name="libelle" value="{{$prescription->libelle}}" 
                 onchange="refreshListProtocole(this.form);" />
        
          <button class="tick notext" type="button"></button>
          <button type="button" class="search" onclick="Protocole.preview('{{$prescription->_id}}')">Visualiser</button>
           
          <br />
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
        </form>
          
        <form name="duplicate">
          <button type="button" class="submit" onclick="Protocole.duplicate('{{$prescription->_id}}')">Dupliquer</button> 
        </form>
      {{else}}
         <!-- Prescription du Dr {{$prescription->_ref_praticien->_view}}<br /> -->
        {{$prescription->_ref_object->_view}}
        {{if $prescription->_ref_patient->_age}}
           ({{$prescription->_ref_patient->_age}} ans - {{$prescription->_ref_patient->naissance|date_format:"%d/%m/%Y"}}{{if $poids}} - {{$poids}} kg{{/if}})
        {{/if}}
      {{/if}}
    </th>
  </tr>
  
  <tr>
    {{if !$mode_protocole && !$mode_pharma}}
    {{*if !$mode_protocole && !$mode_pharma && $prescription->_can_add_line*}}
    <th class="category">Protocoles</th>
    {{/if}}
    
    {{*if $prescription->_can_add_line && !$mode_protocole*}}
    {{if !$mode_protocole}}
      <th class="category">Date d'ajout de lignes</th>
    {{/if}}
    <th class="category">Outils</th>
  </tr>
  <tr>
  {{if !$mode_protocole && !$mode_pharma}}
  {{*if !$mode_protocole && !$mode_pharma && $prescription->_can_add_line*}}
   <td class="date" style="text-align: right;">
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?">
	      <table class="form">
	        <tr>
		        <td>
			        Protocoles de {{$prescription->_ref_current_praticien->_view}}
			        <input type="hidden" name="m" value="dPprescription" />
			        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
			        <input type="hidden" name="del" value="0" />
			        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			        <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			        <select name="pack_protocole_id" style="width: 100px;">
			          <option value="">&mdash; Sélection</option>
			          {{if $protocoles_praticien|@count || $packs_praticien|@count}}
				          <optgroup label="Praticien">
					          {{foreach from=$protocoles_praticien item=_protocole_praticien}}
					          <option value="prot-{{$_protocole_praticien->_id}}">{{$_protocole_praticien->_view}}</option>
					          {{/foreach}}
						        {{foreach from=$packs_praticien item=_pack_praticien}}
						        <option value="pack-{{$_pack_praticien->_id}}" style="font-weight: bold">{{$_pack_praticien->_view}}</option>
						        {{/foreach}}
				          </optgroup>
			          {{/if}}
			          {{if $protocoles_function|@count || $packs_function|@count}}
			            <optgroup label="Cabinet">
			              {{foreach from=$protocoles_function item=_protocole_function}}
			              <option value="prot-{{$_protocole_function->_id}}">{{$_protocole_function->_view}}</option>
			              {{/foreach}}
			              {{foreach from=$packs_function item=_pack_function}}
				            <option value="pack-{{$_pack_function->_id}}" style="font-weight: bold">{{$_pack_function->_view}}</option>
				            {{/foreach}}
			            </optgroup>
			          {{/if}}
			        </select>
							<br />
				 				{{if $prescription->type != "externe"}}
				 				Intervention
				 				  <select name="operation_id">
				 				    {{foreach from=$prescription->_dates_dispo key=operation_id item=_date_operation}}
				 				      <option value="{{$operation_id}}">{{$_date_operation|date_format:$dPconfig.datetime}}</option>
				 				    {{/foreach}}
 									</select>
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
					 				    prepareForm("applyProtocole");
					            Calendar.regField('applyProtocole', "debut", false, dates);
					          } );
					 				</script>				 				
				 				{{/if}}
			 				
			          <button type="button" class="submit" onclick="submitProtocole(this.form);">Appliquer</button>
		        </td>
	        </tr>
	      </table>
      </form>
    </td>  
  {{/if}}
  
  {{*if $prescription->_can_add_line*}}
  {{if !$mode_protocole}}
      <td class="date">
        <form name="selDateLine" action="?" method="get"> 
      
        {{if $prescription->type != "externe"}}   
	        <select name="debut_date" 
					        onchange="$('selDateLine_debut_da').innerHTML = new String;
	 				                    this.form.debut.value = '';
	 				          				  if(this.value == 'other') { 
	 				          					  $('calendarProt').show();
	 				          				  } else { 			    
	 				          				    this.form.debut.value = this.value;
	 				          				    $('calendarProt').hide();
	 				          				  }">
	     				  
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
		       prepareForm(document.selDateLine);
		       Calendar.regField("selDateLine", "debut", false);
	    	} );
        </script>	
	    </form>
	    </td>
	  {{/if}} 
	{{*/if*}}
 
    <td style="text-align: left;">
       {{if !$mode_protocole}}
       <div id="antecedent_allergie">
				     {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
				     {{assign var=sejour_id value=$prescription->_ref_object->_id}}
				     {{include file="../../dPprescription/templates/inc_vw_antecedent_allergie.tpl"}}    
			 </div>   
      {{/if}}

      <select name="affichageImpression" onchange="Prescription.popup('{{$prescription->_id}}', this.value); this.value='';">
        <option value="">&mdash; Action</option>
	 		  <!-- Impression de la prescription -->
	 		  <optgroup label="Imprimer">
			  {{if $prescription->type != "sortie"}}
			  <option value="printPrescription">Prescription</option>
			  {{/if}}
        {{if ($prescription->type != "externe") && $prescription->object_id}}
          <option value="printOrdonnance">Ordonnance</option>
        {{/if}}
        </optgroup>
        <optgroup label="Afficher">
      	  <option value="viewAlertes">Alertes</option>
      		{{if $prescription->object_id}}
      		<option value="viewHistorique">Historique</option>
      		<option value="viewSubstitutions">Substitutions</option>
      	  {{/if}}
        </optgroup>
      </select>

      <button class="new" type="button" onclick="viewEasyMode('{{$mode_protocole}}','{{$mode_pharma}}', menuTabs.activeContainer.id);">Mode grille</button>
       
      <br />
      {{if $prescription->object_id}}
        <select name="advAction">
          <option value="">&mdash; Traitements perso</option>
          <option value="stopPerso" onclick="Prescription.stopTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Arrêter</option>
          <option value="goPerso" onclick="Prescription.goTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Reprendre</option>
        </select>
      {{/if}}
      
      {{if $mode_pharma && $prescription->_score_prescription == "2"}}
        <strong>Validation auto. impossible</strong>
      {{/if}}
      
      {{if $mode_pharma}}
        <strong>
          {{mb_label object=$prescription field=_score_prescription}} {{mb_value object=$prescription field=_score_prescription}}
        </strong>
      {{/if}}
    </td>
  </tr>  
  {{if $praticien_sortie_id && $prescription->_praticiens|@count > 1}}
  <tr>
    <th class="category" style="background-color: #B00; color: #fff" colspan="3">Prescription affichée partiellement :<br />certaines lignes ont été prescrites par d'autres praticiens</th>
  </tr>
  {{/if}}
</table>