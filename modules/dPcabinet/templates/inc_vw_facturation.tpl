<!-- Facture -->
{{if $facture && $facture->_id}}
<fieldset>
  <legend>{{tr}}{{$facture->_class}}{{/tr}}: {{$facture}}</legend>
  <table class="main tbl">

    {{if $facture->cloture}}
    <tr>
      <td colspan="10">
        <div class="small-info">
          <strong>La facture est cloturée.</strong>
          Pour pouvoir ajouter des éléments, veuillez la rouvrir.
        </div>
      </td>
    </tr>
    {{/if}}
    
    {{mb_include module=cabinet template=inc_vw_facturation_tarmed}}
    {{mb_include module=cabinet template=inc_vw_facturation_t2a   }}

  </table> 
</fieldset>
{{/if}}

<!-- Reglement -->
{{if ($facture->_id && $facture->cloture) 
  || (!$facture->_id && $consult && $consult->tarif && $consult->valide) 
}}
  <div id="reglements_facture">
    {{mb_include module=cabinet template="inc_vw_reglements"}}
  </div>
{{/if}}