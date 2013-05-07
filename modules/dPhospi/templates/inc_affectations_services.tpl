<table class="tbl">
  <tr>
    <th {{if $curr_service->externe}}class="service_externe"{{/if}}>
      {{$curr_service->nom}}
      <br />
      <span style="font-size: 80%;">
      {{if $curr_service->externe}}
        externe
      {{else}}
        {{$curr_service->_nb_lits_dispo}} lit(s) dispo
      {{/if}}
      </span>
    </th>
  </tr>
</table>
{{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
  {{if $curr_chambre->annule == 0}}
    {{mb_include module=hospi template=inc_affectations_chambres}}
  {{/if}}
{{/foreach}}