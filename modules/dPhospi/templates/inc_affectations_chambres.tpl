<table class="tbl chambrecollapse" id="chambre-{{$curr_chambre->_id}}">
  <tr>
    <th class="chambre" colspan="2" onclick="
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
      <img src="images/icons/surb.png" alt="warning" title="Over-booking: {{$curr_chambre->_overbooking}} collisions" />
      {{/if}}

      {{if $curr_chambre->_ecart_age > 15}}
      <img src="images/icons/age.png" alt="warning" title="Ecart d'âge important: {{$curr_chambre->_ecart_age}} ans" />
      {{/if}}

      {{if $curr_chambre->_genres_melanges}}
      <img src="images/icons/sexe.png" alt="warning" title="Sexes opposés" />
      {{/if}}

      {{if $curr_chambre->_chambre_seule}}
      <img src="images/icons/seul.png" alt="warning" title="Chambre seule obligatoire" />
      {{/if}}
      
      {{if $curr_chambre->_chambre_double}}
      <img src="images/icons/double.png" alt="warning" title="Chambre double possible" />
      {{/if}}

      {{if $curr_chambre->_conflits_chirurgiens}}
      <img src="images/icons/prat.png" alt="warning" title="{{$curr_chambre->_conflits_chirurgiens}} Conflit(s) de praticiens" />
      {{/if}}

      {{if $curr_chambre->_conflits_pathologies}}
      <img src="images/icons/path.png" alt="warning" title="{{$curr_chambre->_conflits_pathologies}} Conflit(s) de pathologies" />
      {{/if}}
      
      {{if $curr_chambre->annule == 1}}
      <img src="images/icons/annule.png" alt="warning" title="Chambre plus utilisée" />
      {{/if}}

      <a name="chambre{{$curr_chambre->_id}}" style="font-weight: bold;">
        {{$curr_chambre->nom}}
      </a>
    </th>
  </tr>
  {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
    {{include file="inc_affectations_lits.tpl"}}
  {{/foreach}}
</table>