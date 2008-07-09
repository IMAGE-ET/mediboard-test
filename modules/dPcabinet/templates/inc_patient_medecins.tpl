            {{if $patient->medecin_traitant}}
              <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$patient->medecin_traitant}} } });">
                Dr {{$patient->_ref_medecin_traitant->_view}}
              </span>
            {{/if}}
            {{if $patient->medecin1}}
              <br />
              <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$patient->medecin1}} } });">
                Dr {{$patient->_ref_medecin1->_view}}
              </span>
            {{/if}}
            {{if $patient->medecin2}}
              <br />
              <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$patient->medecin2}} } });">
                Dr {{$patient->_ref_medecin2->_view}}
              </span>
            {{/if}}
            {{if $patient->medecin3}}
              <br />
              <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$patient->medecin3}} } });">
                Dr {{$patient->_ref_medecin3->_view}}
              </span>
            {{/if}}
            {{if $consult->adresse}}
              <br />
              <i>(Patient adressé)</i>
            {{/if}}