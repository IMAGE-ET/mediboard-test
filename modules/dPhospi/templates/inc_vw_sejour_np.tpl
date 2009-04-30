{{if $curr_sejour->_id != ""}}
<tr {{if $object->_id == $curr_sejour->_id}}class="selected"{{/if}}>
  <td>
  <a href="#1" onclick="popEtatSejour({{$curr_sejour->_id}});">
    <img src="images/icons/jumelle.png" alt="edit" title="Etat du Séjour" />
  </a>
  </td>
  <td>
  {{assign var=prescriptions value=$curr_sejour->_ref_prescriptions}}
  {{assign var=prescription_sejour value=$prescriptions.sejour}}
  {{assign var=prescription_sortie value=$prescriptions.sortie}}
  

  <a class="text" href="#1" onclick="markAsSelected(this); addSejourIdToSession('{{$curr_sejour->_id}}'); loadViewSejour({{$curr_sejour->_id}},{{$curr_sejour->praticien_id}},{{$curr_sejour->patient_id}},'{{$date}}')">
    <span class="{{if !$curr_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $curr_sejour->septique}}septique{{/if}}">{{$curr_sejour->_ref_patient->_view}}</span>
  </a>
  <script type="text/javascript">
    ImedsResultsWatcher.addSejour('{{$curr_sejour->_id}}', '{{$curr_sejour->_num_dossier}}');
  </script>
  </td>
  <td>
    <a href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sejour->_ref_patient->_id}}">
      <img src="images/icons/edit.png" alt="edit" title="Editer le patient" />
    </a>
    </td>
    <td>
    <a href="{{$curr_sejour->_ref_patient->_dossier_cabinet_url}}&amp;patient_id={{$curr_sejour->_ref_patient->_id}}">
      <img src="images/icons/search.png" alt="view" title="Afficher le dossier complet" />
    </a>                             
  </td>
  <td>
    <div id="labo_for_{{$curr_sejour->_id}}" style="display: none">
      <img src="images/icons/labo.png" alt="Labo" title="Résultats de laboratoire disponibles" />
    </div>
    <div id="labo_hot_for_{{$curr_sejour->_id}}" style="display: none">
      <img src="images/icons/labo_hot.png" alt="Labo" title="Résultats de laboratoire disponibles" />
    </div>
  </td>
  <td class="action">
  <div class="mediuser" style="border-color:#{{$curr_sejour->_ref_praticien->_ref_function->color}}">
    {{$curr_sejour->_ref_praticien->_shortview}}
 
    {{if $isPrescriptionInstalled}}         
    	 {{if $prescription_sejour->_id && (!$prescription_sortie->_id || $prescription_sejour->_counts_no_valide)}}
    	   <img src="images/icons/warning.png" alt="" title="" 
    	   			onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-alertes-{{$curr_sejour->_guid}}")'/>
    	 {{/if}}
    	 
    	 <div id="tooltip-content-alertes-{{$curr_sejour->_guid}}" style="display: none;">
    	   <ul>
	    	 {{if !$prescription_sortie->_id}}
		       <li>Ce séjour ne possède pas de prescription de sortie</li>
	       {{/if}}
         {{if $prescription_sejour->_counts_no_valide}}
           <li>Lignes non validées dans la prescription de séjour</li>
         {{/if}}    
    	 </div>    
    {{/if}}             
    </div>
  </td>
</tr>
{{/if}}