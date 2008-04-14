<script type="text/javascript">

preselectType = function(contexte, oForm){
  if(!oForm){
    var oForm = document.addProtocolePresc; 
  }
  
  {{if $contexteType}}
	  contexteType = {{$contexteType|@json}};
	  _contexteType = contexteType[contexte];
	
	  if(contexte == "CConsultation"){
	    oForm.type.value = "externe";
	  } else {
	    oForm.type.value = "pre_admission";
	  }
	  
	  $A(oForm.type).each( function(input) {
	    input.disabled = !_contexteType.include(input.value);
	  });
  {{/if}}
}

</script>



<!-- Formulaire de creation du protocole -->
{{if !$prescription->_id && $mode_protocole && !$mode_pharma}}
<form action="?m=dPprescription" method="post" name="addProtocolePresc" onsubmit="return Protocole.add();">	
   <input type="hidden" name="m" value="dPprescription" />
   <input type="hidden" name="dosql" value="do_prescription_aed" />
   <input type="hidden" name="prescription_id" value="" />
   <input type="hidden" name="del" value="0" />
   <input type="hidden" name="object_class" value=""/>
   <input type="hidden" name="object_id" value=""/>
   <input type="hidden" name="praticien_id" value="" />
   {{if $prescription->object_class == "CConsultation"}}
     <input type="hidden" name="type" value="externe" />
   {{/if}}
   {{if $prescription->object_class == "CSejour"}}
     <input type="hidden" name="type" value="pre_admission" />
   {{/if}}
   <input type="hidden" name="callback" value="Prescription.reloadAddProt" />
   <table class="form">
     <tr>
       <th class="category" colspan="2">
         Création d'un protocole
       </th>
    </tr>
    <tr>
      <th>  
        {{mb_label object=$protocole field="libelle"}}
      </th>
      <td>
		    {{mb_field object=$protocole field="libelle" class="notNull"}}  
      </td>
    </tr>
    <tr>
      <th>
			  {{mb_label object=$protocole field="object_class"}}
			</th>
			<td>
			  {{mb_field object=$protocole field="object_class" onchange="preselectType(this.value)"}}  
			</td>
	  </tr>
	  <tr>
	    <th>
	      {{mb_label object=$protocole field="type"}}
	    </th>
	    <td>
	      <select name="type">
	        <option value="pre_admission">Pré-admission</option>
	        <option value="sejour">Séjour</option>
	        <option value="sortie">Sortie</option>
	        <option value="externe">Externe</option>
	      </select>  
	    </td>
	  </tr>
	  <tr>
	   <td colspan="2" style="text-align: center">
			  <button type="button" onclick="this.form.onsubmit();" class="new">Créer une protocole</button>
	   </td>  
	  </tr>
  </table>
</form>
{{/if}}


<!-- Formulaire de création de la prescription -->
{{if !$prescription->_id && !$mode_protocole && !$mode_pharma}}
<form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_aed" />
  <input type="hidden" name="prescription_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  
  <select name="praticien_id">
    {{foreach from=$listPrats item=curr_prat}}
    <option value="{{$curr_prat->_id}}">
      {{$curr_prat->_view}}
    </option>
    {{/foreach}}
  </select>
  
  <!-- Choix du type de la prescription -->
  {{if $prescription->object_class == "CConsultation"}}
     <input type="hidden" name="type" value="externe" />
  
    {{assign var=prescriptions value=$prescription->_ref_object->_ref_prescriptions}}
    
    {{if array_key_exists('externe', $prescriptions)}}              
      {{assign var=prescription_externe value=$prescriptions.externe}}
      {{if $prescription_externe->_id}}
        <div class="big-info">
			    Il n'est pas possible de créer plus d'une prescription par consultation
			    <a href="?m=dPprescription&tab=vw_edit_prescription&prescription_id={{$prescription_externe->_id}}"><br />
			    Accéder à la prescription déja créée</a>
			  </div>
			{{else}}  
      <button type="submit" class="new">Créer une prescription pour la consultation</button>
      {{/if}}
    {{/if}}
  {{else}}
    <select name="type">
      <option value="pre_admission">Pre-admission</option>
      <option value="sejour">Séjour</option>
      <option value="sortie">sortie</option>
    </select>
    <button type="submit" class="new">Créer une prescription pour le séjour</button>
  {{/if}}
</form>
{{/if}}

{{if !$prescription->_id && $mode_pharma}}
  <div class="big-info">
    Veuillez sélectionner un séjour pour pouvoir accéder à sa prescription.
  </div>
{{/if}}


{{if $prescription->_id}}
  <!-- Affichage de l'entete de la prescription -->
  {{include file="../../dPprescription/templates/inc_header_prescription.tpl"}}	
	<!-- Affichage des elements de la prescription -->
	<div id="produits_elements">
	  {{include file="../../dPprescription/templates/inc_vw_produits_elements.tpl"}}  
	</div>
{{/if}}


<script type="text/javascript">
// Preparation du formulaire de creation de protocole
if(document.addProtocolePresc){
  prepareForm(document.addProtocolePresc);  
  preselectType("CConsultation");
}
</script>