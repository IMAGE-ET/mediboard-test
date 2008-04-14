<table class="form">
  <tr>
    <th class="title" colspan="2">
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
  <tr>
  <td>

  

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
   
   {{assign var=antecedents value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents}}
   {{if $antecedents}}
   <button type="button" class="warning" onclick="Prescription.viewAllergies('{{$prescription->_id}}')">
     Allergies
   </button>
   {{/if}}
   {{/if}} 
   </td>
   
       
  {{if !$mode_protocole}}
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
          {{foreach from=$protocoles item=protocole}}
          <option value="{{$protocole->_id}}">{{$protocole->_view}}</option>
          {{/foreach}}  
        </select>
      </form>
  </td>  
  {{/if}}
  
  </tr>  

    
</table>