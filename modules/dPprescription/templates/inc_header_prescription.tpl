<script type="text/javascript">

refreshListProtocole = function(oForm){
  var oFormFilter = document.selPrat;
  oFormFilter.praticien_id.value = oForm.praticien_id.value;
  oFormFilter.function_id.value = oForm.function_id.value;
  
  if(oFormFilter.praticien_id.value || oFormFilter.function_id.value){
	  submitFormAjax(oForm, 'systemMsg', { 
	        onComplete : function() { 
	           Protocole.refreshList(oForm.praticien_id.value,oForm.prescription_id.value, oForm.function_id.value) 
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
  var oForm = document.applyProtocole;
  if(oForm.debut_date){
	  var debut_date = oForm.debut_date.value;
	  if(debut_date != "other"){
	    oForm.debut.value = debut_date;
	  }
  }
	if(document.selPraticienLine){
   oForm.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  submitFormAjax(oForm, 'systemMsg');
  oForm.protocole_id.value = '';
  oForm.debut.value = '';
}

</script>

{{assign var=praticien value=$prescription->_ref_praticien}}

<table class="form">
  <tr>
    <th class="title" colspan="2">
    
      <!-- Selection du praticien prescripteur de la ligne -->
			{{if !$is_praticien && !$mode_protocole && $prescription->_can_add_line}}
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
        <input type="text" name="libelle" value="{{$prescription->libelle}}" 
               onchange="refreshListProtocole(this.form);" />
      
        <button class="tick notext" type="button"></button>
      
         <!-- Modification du pratcien_id / user_id -->
         <select name="praticien_id" onchange="this.form.function_id.value=''; refreshListProtocole(this.form)">
          <option value="">&mdash; Sélection d'un praticien</option>
	        {{foreach from=$praticiens item=praticien}}
	        <option class="mediuser" 
	                style="border-color: #{{$praticien->_ref_function->color}};" 
	                value="{{$praticien->_id}}"
	                {{if $praticien->_id == $prescription->praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
	        </option>
	        {{/foreach}}
	      </select>
	      
	      <select name="function_id" onchange="this.form.praticien_id.value=''; refreshListProtocole(this.form)">
          <option value="">&mdash; Choix du cabinet</option>
          {{foreach from=$functions item=_function}}
          <option class="mediuser" style="border-color: #{{$_function->color}}" value="{{$_function->_id}}" 
          {{if $_function->_id == $prescription->function_id}}selected=selected{{/if}}>{{$_function->_view}}</option>
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
    <td>
		  <!-- Impression de la prescription -->
		  {{if $prescription->type != "sortie"}}
		  <button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}')" style="float: left">
		    Prescription
		  </button>
		  {{/if}}
	    {{if !$mode_protocole}}
		     {{if $prescription->type != "externe"}}
				   <!-- Impression de la prescription -->
				   <button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}','ordonnance')" style="float: left">
				     Ordonnance
				   </button>
			   {{/if}}
			   
			   <!-- Affichage du recapitulatif des alertes -->
			   <button type="button" class="search" onclick="Prescription.viewFullAlertes('{{$prescription->_id}}')" style="float: left">
			     Alertes
			   </button>
			   
			   <!-- Affichage de l'historique -->
			   <button type="button" class="search" onclick="Prescription.viewHistorique('{{$prescription->_id}}','historique')" style="float: left">
			     Historique
			   </button>
			
			   <!-- Affichage de l'historique -->
			   <button type="button" class="search" onclick="Prescription.viewHistorique('{{$prescription->_id}}','substitutions')" style="float: left">
			     Substitutions
			   </button>
			 
			   <div id="antecedent_allergie">
			     {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
			     {{assign var=sejour_id value=$prescription->_ref_object->_id}}
			     {{include file="../../dPprescription/templates/inc_vw_antecedent_allergie.tpl"}}    
				 </div>
		   {{/if}}
    </td>
   

  {{if !$mode_protocole && !$mode_pharma && $prescription->_can_add_line}}
   <td style="text-align: right;">
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?">
	      <table class="form" style="border: 2px ridge #68C;">
	        <tr>
		        <td>
			        Protocoles de {{$prescription->_ref_current_praticien->_view}}
			        <input type="hidden" name="m" value="dPprescription" />
			        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
			        <input type="hidden" name="del" value="0" />
			        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			        <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			        <select name="protocole_id">
			          <option value="">&mdash; Sélection d'un protocole</option>
			          {{if $protocoles_praticien|@count}}
			          <optgroup label="Praticien">
			          {{foreach from=$protocoles_praticien item=_protocole_praticien}}
			          <option value="{{$_protocole_praticien->_id}}">{{$_protocole_praticien->_view}}</option>
			          {{/foreach}}
			          </optgroup>
			          {{/if}}
			          {{if $protocoles_function|@count}}
			          <optgroup label="Cabinet">
			          {{foreach from=$protocoles_function item=_protocole_function}}
			          <option value="{{$_protocole_function->_id}}">{{$_protocole_function->_view}}</option>
			          {{/foreach}}
			          </optgroup>
			          {{/if}}
			        </select>
			        </td>
			        <td class="date">
				 				{{if $prescription->type != "externe"}}
				 				  <select name="debut">
				 				    {{foreach from=$prescription->_ref_object->_dates_operations item=_date_operation}}
				 				      <option value="{{$_date_operation}}">{{$_date_operation|date_format:"%d/%m/%Y"}}</option>
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
					 				    prepareForm(document.applyProtocole);
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
  
  </tr>  

</table>