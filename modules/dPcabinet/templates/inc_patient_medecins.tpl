{{if $patient->medecin_traitant}}
  <div class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$patient->medecin_traitant}} } });">
    <strong>{{$patient->_ref_medecin_traitant->_view}}</strong>
  </div>
{{/if}}
{{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
  <div class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$curr_corresp->medecin_id}} } });">
    {{$curr_corresp->_ref_medecin->_view}}
  </div>
{{/foreach}}
{{if $consult->adresse}}
  <i>(Patient adressé)</i>
{{/if}}