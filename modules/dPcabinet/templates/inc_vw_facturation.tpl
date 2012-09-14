<!-- Facture -->
{{if $facture && $facture->_id}}
  <table class="main tbl">
    <th class="title" colspan="10">
      {{$facture}}
    </th>

    {{if $facture->cloture}}
    <tr>
      <td colspan="10">
        <div class="small-info">
        La facture est terminée.<br />
        Pour pouvoir ajouter des éléments, veuillez rouvrir la facture.
        </div>
      </td>
    </tr>
    {{/if}}
    
    {{mb_include module=cabinet template=inc_view_facturation_tarmed}}
    {{mb_include module=cabinet template=inc_view_facturation_t2a   }}

  </table> 
{{/if}}

<!-- Reglement -->
{{if ($facture->_id && $facture->cloture) 
  || (!$facture->_id && $consult && $consult->tarif && $consult->valide) 
}}
  <div id="reglements_facture">
    {{mb_include module=cabinet template="inc_vw_reglements"}}
  </div>
{{/if}}