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
{{if !$prescription->_id && $mode_protocole}}
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
{{if !$prescription->_id && !$mode_protocole}}
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
  {{else}}
    <select name="type">
      <option value="pre_admission">Pre-admission</option>
      <option value="sejour">Séjour</option>
      <option value="sortie">sortie</option>
    </select>
  {{/if}}
  
  {{assign var=prescriptions value=$prescription->_ref_object->_ref_prescriptions}}
  {{assign var=prescription_externe value=$prescriptions.externe}}
  {{if $prescription->object_class == "CConsultation" && $prescription_externe->_id}}              
  
  <div class="big-info">
    Il n'est pas possible de créer plus d'une prescription par consultation
    <a href="?m=dPprescription&tab=vw_edit_prescription&prescription_id={{$prescription_externe->_id}}"><br />
    Accéder à la prescription déja créée</a>
  </div>
  {{else}}
    <button type="submit" class="new">Créer une prescription</button>
  {{/if}}
  
  
</form>
{{/if}}


<!-- Affichage de la prescription -->    
{{if $prescription->_id}}
<table class="form">
  <tr>
    <th class="title">
    
      {{if !$mode_protocole && $prescription->object_class == "CSejour"}}
        <div style="float:left; padding-right: 5px;" class="noteDiv {{$prescription->_ref_object->_class_name}}-{{$prescription->_ref_object->_id}};">
          <img alt="Ecrire une note" src="images/icons/note_grey.png" />
        </div>
      {{/if}}
     
      {{if !$mode_protocole && !$dialog}}
      <button type="button" class="cancel" onclick="Prescription.close('{{$prescription->object_id}}','{{$prescription->object_class}}')" style="float: left">
        Fermer 
      </button>
      {{/if}}
     
      <!-- Impression de la prescription -->
      <button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}')" style="float: left">
        Prescription
      </button>
      
      <!-- Impression de la prescription -->
      <button type="button" class="print" onclick="Prescription.printPrescription('{{$prescription->_id}}','ordonnance')" style="float: left">
        Ordonnance
      </button>
      
      {{if !$mode_protocole}}
      <!-- Affichage du recapitulatif des alertes -->
      <button type="button" class="search" onclick="Prescription.viewFullAlertes('{{$prescription->_id}}')" style="float: left">
        Alertes
      </button>
      
      <!-- Affichage des allergies du patient -->
      {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
      
      {{if $antecedents}}
      {{if array_key_exists('alle', $antecedents)}}
        {{assign var=allergies value=$antecedents.alle}}
		      <span style="float: left">
		        <a href="#allergies-{{$prescription->_id}}"
		           onmouseover="$('allergies-{{$prescription->_id}}').show();"
			         onmouseout="$('allergies-{{$prescription->_id}}').hide();">
			       <img src="images/icons/warning.png" alt="Allergies" title="allergies" />
			       Allergies
			     </a>
		      </span>
			          
		      <div id="allergies-{{$prescription->_id}}" class="tooltip" style=" text-align: left; font-size: 12px; display: none; color: black; background-color: #eee; border-style: ridge; margin-top: 20px; padding-right:5px; ">
			      {{include file="../../dPprescription/templates/inc_vw_allergies.tpl"}}
			    </div>  
		    {{/if}}
		    {{/if}}       
      {{/if}} 
      
      
      
      
      
      
      {{if $mode_protocole}}
      <!-- Formulaire de modification du libelle de la prescription -->
      <form name="addLibelle-{{$prescription->_id}}" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
        <input type="text" name="libelle" value="{{$prescription->libelle}}" 
               onchange="submitFormAjax(this.form, 'systemMsg', { 
                 onComplete : function() { 
                   Protocole.refreshList('{{$prescription->praticien_id}}','{{$prescription->_id}}') 
                 } })" />
      </form>
      <button class="tick notext"></button>
      {{else}}
        {{$prescription->_view}} <br />
     
     
        {{$prescription->_ref_patient->_view}}
     
        {{if $prescription->_ref_patient->_age}}
           ({{$prescription->_ref_patient->_age}} ans - {{$prescription->_ref_patient->naissance|date_format:"%d/%m/%Y"}})
        {{/if}}
     
      {{/if}}
     
     
    </th>
  </tr>
  {{if !$mode_protocole}}
  <tr>
    <td>
      Protocoles de {{$praticien->_view}}
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post" action="?">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
        <select name="protocole_id" onchange="submitFormAjax(this.form, 'systemMsg'); this.value='';">
          <option value="">&mdash; Sélection d'un protocole</option>
          {{foreach from=$protocoles item=protocole}}
          <option value="{{$protocole->_id}}">{{$protocole->_view}}</option>
          {{/foreach}}  
        </select>
      </form>
    </td>
  </tr>
  {{/if}}
</table>

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