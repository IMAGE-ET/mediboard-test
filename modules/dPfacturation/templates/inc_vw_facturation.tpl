<!-- Facture -->
<fieldset class="hatching">
  {{if $facture && $facture->_id}}
  <legend>{{tr}}{{$facture->_class}}{{/tr}}: {{$facture}}</legend>
  <table class="main tbl">
    <tr>
      <td style="text-align:center;">
        <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_ref_patient->_guid}}')">
          Patient : {{$facture->_ref_patient}}
        </a>
      </td>
      <td style="text-align:center;">
        <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_ref_patient->_guid}}')">
          Praticien: {{$facture->_ref_praticien}}
        </a>
      </td>
    </tr>
  </table>
  <table class="main tbl">
    {{if $facture->annule}}
      <tr>
        <td colspan="10">
          <div class="small-warning">
            <strong>La facture est extournée.</strong>
          </div>
        </td>
      </tr>
    {{elseif $facture->cloture}}
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

{{if $facture->_ref_relances|@count && $conf.dPfacturation.CRelance.use_relances}}
  <!-- Relances -->
  <div id="relances">
    {{mb_include module=dPfacturation template="inc_vw_relances"}}
  </div>
{{/if}}

<!-- Reglements -->
{{if $facture->_id && $facture->cloture && !$facture->annule}}
  <div id="reglements_facture">
    {{mb_include module=dPfacturation template="inc_vw_reglements"}}
  </div>
{{/if}}