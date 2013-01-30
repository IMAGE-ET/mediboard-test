<tr>
  <td style="text-align:center;width:100px;">
    {{if $_acte_tarmed->date}}
      {{mb_value object=$_acte_tarmed field="date"}}
    {{elseif $object->_date}}
      {{$object->_date|date_format:"%d/%m/%Y"}}
    {{else}}
      {{$object->date|date_format:"%d/%m/%Y"}}
    {{/if}}
  </td>
  {{if $_acte_tarmed->code}} 
  <td class="acte-{{$_acte_tarmed->_class}}">
     {{mb_value object=$_acte_tarmed field="code"}}
  </td>
  {{else}}
  <td>
  </td>
  {{/if}}
  <td class="compact" style="white-space: pre-line;">
    {{if $_acte_tarmed->libelle}}
      {{$_acte_tarmed->libelle}}
    {{else}}
      {{$_acte_tarmed->_ref_tarmed->libelle}}
    {{/if}}
  </td>
  <td style="text-align:right;">
    {{if $_acte_tarmed->quantite}}
      {{$_acte_tarmed->montant_base/$_acte_tarmed->quantite|string_format:"%0.2f"}}
    {{else}}
      {{$_acte_tarmed->montant_base|string_format:"%0.2f"}}
    {{/if}}
  </td>
  <td style="text-align:right;">{{mb_value object=$_acte_tarmed field="quantite"}}</td>
  <td style="text-align:right;">{{$facture->_coeff}}</td>
  <td style="text-align:right;">{{$_acte_tarmed->montant_base*$facture->_coeff|string_format:"%0.2f"|currency}}</td>
</tr>