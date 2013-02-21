<!-- Facture -->
<fieldset>
  {{if $facture && $facture->_id}}
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
    
    {{mb_include module=dPfacturation template=inc_vw_facturation_tarmed}}
    {{mb_include module=dPfacturation template=inc_vw_facturation_t2a   }}
  </table>
  
  {{else}}
    <legend class="empty">{{tr}}CFactureCabinet.none{{/tr}}</legend>
  {{/if}}
</fieldset>

<!-- Reglement -->
{{if $facture->_id && $facture->cloture}}
  <div id="reglements_facture">
    {{mb_include module=dPfacturation template="inc_vw_reglements"}}
  </div>
{{/if}}