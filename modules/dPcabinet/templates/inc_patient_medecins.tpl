{{assign var=medecin value=$patient->_ref_medecin_traitant}}
{{if $medecin->_id}}
  <div class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
    <strong>{{$medecin}}</strong>
  </div>
{{/if}}

{{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
{{assign var=medecin value=$curr_corresp->_ref_medecin}}
  <div class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
    {{$medecin}}
  </div>
{{/foreach}}

{{if $consult->adresse}}
  <em>(Patient adressé)</em>
{{/if}}