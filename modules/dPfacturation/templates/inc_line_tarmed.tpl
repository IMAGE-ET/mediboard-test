{{if !$conf.dPfacturation.Other.use_view_quantitynull || ($conf.dPfacturation.Other.use_view_quantitynull && $_acte_tarmed->quantite != 0)}}
  <tr>
    <td style="text-align:center;width:100px;">
      {{if $facture->_ref_last_sejour->_id}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_sejour->_guid}}')">
      {{else}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_consult->_guid}}')">
      {{/if}}
      {{if $_acte_tarmed->date}}
        {{mb_value object=$_acte_tarmed field="date"}}
      {{elseif $facture->_ref_last_consult->_date}}
        {{$facture->_ref_last_consult->_date|date_format:"%d/%m/%Y"}}
      {{else}}
        {{$facture->_ref_last_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
      {{/if}}
      </span>
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
        {{$_acte_tarmed->montant_base|string_format:"%0.2f"}}
      {{else}}
        {{$_acte_tarmed->montant_base|string_format:"%0.2f"}}
      {{/if}}
    </td>
    <td style="text-align:right;">{{mb_value object=$_acte_tarmed field="quantite"}}</td>
    <td style="text-align:right;">{{$facture->_coeff}}</td>
    <td style="text-align:right;">{{$_acte_tarmed->montant_base*$facture->_coeff*$_acte_tarmed->quantite|string_format:"%0.2f"|currency}}</td>
  </tr>
{{/if}}