<table class="tbl">
  <tr>
    <th colspan="2">Liste des factures</th>
  </tr>
  {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
    <tr>
      <td style="background-color:#fcc;width:40px;"></td>
      <td class="text">Facture non cloturée</td>
    </tr>
  {{/if}}
  <tr>
    <td style="background-color:#cfc;width:40px;"></td>
    <td class="text">Facture réglée</td>
  </tr>
  <tr>
    <td class="hatching"></td>
    <td class="text">Facture annulée</td>
  </tr>
</table>