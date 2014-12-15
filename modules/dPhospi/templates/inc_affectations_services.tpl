{{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
  {{if $curr_chambre->annule == 0}}
    {{mb_include module=hospi template=inc_affectations_chambres}}
  {{/if}}
{{/foreach}}