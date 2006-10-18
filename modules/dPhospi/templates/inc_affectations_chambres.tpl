        <table class="chambrecollapse" id="chambre{{$curr_chambre->chambre_id}}">
          <tr>
            <th class="chambre" colspan="2" onclick="javascript:flipChambre({{$curr_chambre->chambre_id}});
                {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
                Droppables.add('lit{{$curr_lit->lit_id}}', { 
                  onDrop:function(element){
                    DragDropSejour(element.id,{{$curr_lit->lit_id}})
                  }, 
                  hoverclass:'litselected'
                });
                {{foreach from=$curr_lit->_ref_affectations item=curr_aff}}
                setupCalendar({{$curr_aff->affectation_id}});
                {{/foreach}}
                {{/foreach}}">
              {{if $curr_chambre->_overbooking}}
              <img src="modules/{{$m}}/images/surb.png" alt="warning" title="Over-booking: {{$curr_chambre->_overbooking}} collisions" />
              {{/if}}

              {{if $curr_chambre->_ecart_age > 15}}
              <img src="modules/{{$m}}/images/age.png" alt="warning" title="Ecart d'âge important: {{$curr_chambre->_ecart_age}} ans" />
              {{/if}}

              {{if $curr_chambre->_genres_melanges}}
              <img src="modules/{{$m}}/images/sexe.png" alt="warning" title="Sexes opposés" />
              {{/if}}

              {{if $curr_chambre->_chambre_seule}}
              <img src="modules/{{$m}}/images/seul.png" alt="warning" title="Chambre seule obligatoire" />
              {{/if}}
              
              {{if $curr_chambre->_chambre_double}}
              <img src="modules/{{$m}}/images/double.png" alt="warning" title="Chambre double possible" />
              {{/if}}

              {{if $curr_chambre->_conflits_chirurgiens}}
              <img src="modules/{{$m}}/images/prat.png" alt="warning" title="{{$curr_chambre->_conflits_chirurgiens}} Conflit(s) de praticiens" />
              {{/if}}

              {{if $curr_chambre->_conflits_pathologies}}
              <img src="modules/{{$m}}/images/path.png" alt="warning" title="{{$curr_chambre->_conflits_pathologies}} Conflit(s) de pathologies" />
              {{/if}}

              <strong><a name="chambre{{$curr_chambre->chambre_id}}">{{$curr_chambre->nom}}</a></strong>
            </th>
          </tr>
          {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
            {{include file="inc_affectations_lits.tpl"}}
          {{/foreach}}
        </table>