{{if $curr_sejour->_id != ""}}
<tr {{if $object->_id == $curr_sejour->_id}}class="selected"{{/if}}>
  <td style="padding: 0;">
    <button class="lookup notext" onclick="popEtatSejour({{$curr_sejour->_id}});">Etat du séjour</button>
  </td>
  
  <td>
    {{assign var=prescriptions value=$curr_sejour->_ref_prescriptions}}
    {{assign var=prescription_sejour value=$prescriptions.sejour}}
    {{assign var=prescription_sortie value=$prescriptions.sortie}}

    <a class="text" href="#1" 
       onclick="markAsSelected(this); addSejourIdToSession('{{$curr_sejour->_id}}'); loadViewSejour({{$curr_sejour->_id}},{{$curr_sejour->praticien_id}},{{$curr_sejour->patient_id}},'{{$date}}')">
      <span class="{{if !$curr_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $curr_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_ref_patient->_guid}}');" >
        {{$curr_sejour->_ref_patient->_view}}
      </span>
    </a>
  </td>

  <td style="padding: 1px;" onclick="markAsSelected(this); addSejourIdToSession('{{$curr_sejour->_id}}'); loadViewSejour('{{$curr_sejour->_id}}', {{$curr_sejour->praticien_id}}, {{$curr_sejour->patient_id}}, '{{$date}}'); tab_sejour.setActiveTab('Imeds')">
	  {{if $isImedsInstalled}}
      {{mb_include module=dPImeds template=inc_sejour_labo sejour=$curr_sejour link="#"}}
    {{/if}}
  </td>
  
  <td class="action" style="padding: 1px;">
    <div class="mediuser" style="border-color:#{{$curr_sejour->_ref_praticien->_ref_function->color}}">
      <label title="{{$curr_sejour->_ref_praticien->_view}}">
      {{$curr_sejour->_ref_praticien->_shortview}}
      </label>
    </div>
  </td>
  
  {{if $isPrescriptionInstalled}}
  <td style="padding: 1px;">
	  {{if $prescription_sejour->_id && (!$prescription_sortie->_id || $prescription_sejour->_counts_no_valide)}}
	    <img src="images/icons/warning.png" width="12"
	    			onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-alertes-{{$curr_sejour->_guid}}')" />
	  {{/if}}
	 
	  <div id="tooltip-content-alertes-{{$curr_sejour->_guid}}" style="display: none;">
	    <ul>
  	  {{if !$prescription_sortie->_id}}
        <li>Ce séjour ne possède pas de prescription de sortie</li>
      {{/if}}
      {{if $prescription_sejour->_counts_no_valide}}
        <li>Lignes non validées dans la prescription de séjour</li>
      {{/if}}
      </ul> 
	  </div>
  </td>
  {{/if}}
  
</tr>
{{/if}}