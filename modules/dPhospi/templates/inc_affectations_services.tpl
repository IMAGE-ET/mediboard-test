<table class="tbl">
  <tr>
    <th>
      {{$curr_service->nom}}
      / {{$curr_service->_nb_lits_dispo}} lit(s) dispo
    </th>
  </tr>
</table>
{{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
  {{if $curr_chambre->annule == 0}}
    {{include file="inc_affectations_chambres.tpl"}}
  {{/if}}
{{/foreach}}