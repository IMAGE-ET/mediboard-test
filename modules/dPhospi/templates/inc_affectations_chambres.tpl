<table class="tbl chambrecollapse {{if !$curr_chambre->_nb_affectations}}opacity-70{{/if}}" id="chambre-{{$curr_chambre->_id}}">
  <tr>
    <th class="chambre text" colspan="2" onclick="
        flipChambre({{$curr_chambre->_id}});
        {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
          Droppables.addLit({{$curr_lit->_id}});
        {{foreach from=$curr_lit->_ref_affectations item=curr_aff}}
        Calendar.setupAffectation({{$curr_aff->_id}}, {
          sejour: {
            start: '{{$curr_aff->_ref_sejour->entree_prevue}}',
            stop: '{{$curr_aff->_ref_sejour->sortie_prevue}}'
          },
          currAffect : {
            start : '{{$curr_aff->entree}}',
            stop : '{{$curr_aff->sortie}}'
          },
          outerAffect : {
            start : '{{$curr_aff->_ref_prev->entree}}',
            stop : '{{$curr_aff->_ref_next->sortie}}'
          }
          
        });
        {{/foreach}}
        {{/foreach}}">
      {{if $curr_chambre->_overbooking}}
      <img src="modules/dPhospi/images/surb.png" alt="warning" title="Over-booking: {{$curr_chambre->_overbooking}} collisions" />
      {{/if}}

      {{if $curr_chambre->_ecart_age > 15}}
      <img src="modules/dPhospi/images/age.png" alt="warning" title="Ecart d'âge important: {{$curr_chambre->_ecart_age}} ans" />
      {{/if}}

      {{if $curr_chambre->_genres_melanges}}
      <img src="modules/dPhospi/images/sexe.png" alt="warning" title="Sexes opposés" />
      {{/if}}

      {{if $curr_chambre->_chambre_seule}}
      <img src="modules/dPhospi/images/seul.png" alt="warning" title="Chambre seule obligatoire" />
      {{/if}}
      
      {{if $curr_chambre->_chambre_double}}
      <img src="modules/dPhospi/images/double.png" alt="warning" title="Chambre double possible" />
      {{/if}}

      {{if $curr_chambre->_conflits_chirurgiens}}
      <img src="modules/dPhospi/images/prat.png" alt="warning" title="{{$curr_chambre->_conflits_chirurgiens}} Conflit(s) de praticiens" />
      {{/if}}

      {{if $curr_chambre->_conflits_pathologies}}
      <img src="modules/dPhospi/images/path.png" alt="warning" title="{{$curr_chambre->_conflits_pathologies}} Conflit(s) de pathologies" />
      {{/if}}
      
      {{if $curr_chambre->annule == 1}}
      <img src="modules/dPhospi/images/annule.png" alt="warning" title="Chambre plus utilisée" />
      {{/if}}

      <a name="chambre{{$curr_chambre->_id}}" style="font-weight: bold;" title="{{$curr_chambre->caracteristiques}}">
        {{$curr_chambre->_shortview}}
      </a>
    </th>
  </tr>
  {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
    {{mb_include module=hospi template=inc_affectations_lits}}
  {{/foreach}}
</table>