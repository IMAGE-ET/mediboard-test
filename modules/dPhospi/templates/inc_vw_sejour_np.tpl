{{if $curr_sejour->_id != ""}}
<tr  {{if $object->_id == $curr_sejour->_id}}class="selected '{{$curr_sejour->type }}'" {{else}} class='{{$curr_sejour->type }}' {{/if}}>
  <td style="padding: 0;">
    <button class="lookup notext" style="margin: 0;" onclick="popEtatSejour({{$curr_sejour->_id}});">Etat du séjour</button>
  </td>
  
  <td>
    {{assign var=prescriptions value=$curr_sejour->_ref_prescriptions}}
    {{assign var=prescription_sejour value=$prescriptions.sejour}}
    {{assign var=prescription_sortie value=$prescriptions.sortie}}

    <a class="text" href="#1" 
       onclick="markAsSelected(this); addSejourIdToSession('{{$curr_sejour->_id}}'); loadViewSejour({{$curr_sejour->_id}},'{{$date}}')">
      <span class="{{if !$curr_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $curr_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}');" >
        {{$curr_sejour->_ref_patient->_view}}
      </span>
    </a>
  </td>

  {{if "soins dossier_soins show_ampoule_patient"|conf:"CGroups-$g"}}
    <td></td>
  {{/if}}

  <td style="padding: 1px;">
    <div class="imeds_alert" onclick="markAsSelected(this); addSejourIdToSession('{{$curr_sejour->_id}}'); loadViewSejour('{{$curr_sejour->_id}}', '{{$date}}'); tab_sejour.setActiveTab('Imeds')">
      {{if $isImedsInstalled}}
        {{mb_include module=Imeds template=inc_sejour_labo sejour=$curr_sejour link="#"}}
      {{/if}}
    </div>
    {{mb_include module=dPfiles template=inc_icon_category_check object=$curr_sejour}}
  </td>
  
  <td class="action" style="padding: 1px;">
    <div class="mediuser" style="border-color:#{{$curr_sejour->_ref_praticien->_ref_function->color}}">
      <label title="{{$curr_sejour->_ref_praticien->_view}}">
      {{$curr_sejour->_ref_praticien->_shortview}}
      </label>
    </div>
  </td>
</tr>
{{/if}}