<script type="text/javascript">

refreshListProtocole = function(oForm){

  var oFormFilter = document.selPrat;
  
  oFormFilter.praticien_id.value = oForm.praticien_id.value;
  oFormFilter.function_id.value = oForm.function_id.value;
 
  submitFormAjax(oForm, 'systemMsg', { 
        onComplete : function() { 
           Protocole.refreshList(oForm.praticien_id.value,oForm.prescription_id.value, oForm.function_id.value) 
        } 
  });
}

</script>

<table class="form">
  <tr>
    <th class="title" colspan="2">
      {{if !$mode_protocole && $prescription->object_class == "CSejour"}}
        <div style="float:left; padding-right: 5px;" class="noteDiv {{$prescription->_ref_object->_class_name}}-{{$prescription->_ref_object->_id}};">
          <img alt="Ecrire une note" src="images/icons/note_grey.png" />
        </div>
      {{/if}}
     
      {{if !$mode_protocole && !$dialog && !$mode_pharma}}
      <button type="button" class="cancel" onclick="Prescription.close('{{$prescription->object_id}}','{{$prescription->object_class}}')" style="float: left">
        Fermer 
      </button>
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
        
      {{else}}
        {{$prescription->_view}} <br />
        {{$prescription->_ref_patient->_view}}
        {{if $prescription->_ref_patient->_age}}
           ({{$prescription->_ref_patient->_age}} ans - {{$prescription->_ref_patient->naissance|date_format:"%d/%m/%Y"}}{{if $poids}} - {{$poids}} kg{{/if}})
        {{/if}}
      {{/if}}
      
    </th>
  </tr>
  <tr>
  <td>

  

   <!-- Impression de la prescription -->
   <button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}')" style="float: left">
     Prescription
   </button>
   
   
   
   {{if !$mode_protocole}}
   {{if $prescription->type != "sortie" && $prescription->type != "externe"}}
   <!-- Impression de la prescription -->
   <button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}','ordonnance')" style="float: left">
     Ordonnance
   </button>
   {{/if}}
   
   <!-- Affichage du recapitulatif des alertes -->
   <button type="button" class="search" onclick="Prescription.viewFullAlertes('{{$prescription->_id}}')" style="float: left">
     Alertes
   </button>
   
   

	   {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
	   <!-- Si le dossier medical possède des antecedents -->
	   {{if $antecedents}}
	   
	     <!-- Affichage des allergies -->
		   {{if array_key_exists('alle', $antecedents)}}
		     {{assign var=allergies value=$antecedents.alle}}
		      <img src="images/icons/warning.png" title="Allergies" alt="Allergies" 
		           onmouseover="$('allergies{{$prescription->_id}}').show();"
		           onmouseout="$('allergies{{$prescription->_id}}').hide();" />
		    
		      <div id="allergies{{$prescription->_id}}" class="tooltip" style="display: none; background-color: #ddd; border-style: ridge; padding:3px; left: 174px; top: 110px;">
		        <strong>Allergies</strong>
		        <ul>
			        {{foreach from=$allergies item=allergie}}
			        <li>
					      {{if $allergie->date}}
					 	      {{$allergie->date|date_format:"%d/%m/%Y"}}:
						    {{/if}} 
			  		  	{{$allergie->rques}}
			  	    </li>
			  	    {{/foreach}}
				    </ul>   
		      </div>   
		   {{/if}}
		   
		   <!-- Affichage des autres antecedents -->
	     {{if (array_key_exists('alle', $antecedents) && $antecedents|@count > 1) || ($antecedents|@count >= 1 && !array_key_exists('alle', $antecedents))}}
	      <img src="images/icons/antecedents.gif" title="Antécédents" alt="Antécédents" 
		           onmouseover="$('antecedents{{$prescription->_id}}').show();"
		           onmouseout="$('antecedents{{$prescription->_id}}').hide();" />
		     
		      <div id="antecedents{{$prescription->_id}}" class="tooltip" style="display: none; background-color: #ddd; border-style: ridge; padding:3px; left: 174px; top: 110px;">
		        <ul>
			        {{foreach from=$antecedents key=name item=cat}}
			        {{if $name != "alle"}}
			        <li>
			        <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
			        <ul>
			        {{foreach from=$cat item=ant}}
			        <li>
					      {{if $ant->date}}
					 	      {{$ant->date|date_format:"%d/%m/%Y"}}:
						    {{/if}} 
			  		  	{{$ant->rques}}
			  	    </li>
			  	    {{/foreach}}
			  	    </ul>
			  	    </li>
			  	    {{/if}}
			  	    {{/foreach}}
				    </ul>   
		      </div>  
	     {{/if}}
	   {{/if}} 
   {{/if}}
   </td>
   
       
  {{if !$mode_protocole && !$mode_pharma}}
   <td style="text-align: right">
      Protocoles de {{$praticien->_view}}
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
        <select name="protocole_id" onchange="submitFormAjax(this.form, 'systemMsg'); this.value='';">
          <option value="">&mdash; Sélection d'un protocole</option>
          <optgroup label="Praticien">
          {{foreach from=$protocoles_praticien item=_protocole_praticien}}
          <option value="{{$_protocole_praticien->_id}}">{{$_protocole_praticien->_view}}</option>
          {{/foreach}}
          </optgroup>
          <optgroup label="Cabinet">
          {{foreach from=$protocoles_function item=_protocole_function}}
          <option value="{{$_protocole_function->_id}}">{{$_protocole_function->_view}}</option>
          {{/foreach}}
          </optgroup>
        </select>
      </form>
  </td>  
  {{/if}}
  
  </tr>  

    
</table>