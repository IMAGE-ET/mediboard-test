          {{if $curr_service->_vwService}}
          
          {{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
            {{include file="inc_affectations_chambres.tpl"}}
          {{/foreach}}
          
          {{else}}
          
          Affichage désactivé
          
          {{/if}}