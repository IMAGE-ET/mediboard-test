{{assign var="caisse" value=$_acte_caisse->_ref_caisse_maladie}}
{{if $facture->type_facture == "accident"}}
  {{assign var="coeff_caisse" value=$_acte_caisse->_ref_caisse_maladie->coeff_accident}}
{{else}}
  {{assign var="coeff_caisse" value=$_acte_caisse->_ref_caisse_maladie->coeff_maladie}}
{{/if}}
<tr>
  <td style="text-align:center;width:100px;">
    {{if $facture->_ref_last_sejour->_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_sejour->_guid}}')">
    {{else}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_consult->_guid}}')">
    {{/if}}
    {{if $_acte_caisse->date}}
      {{mb_value object=$_acte_caisse field="date"}}
    {{elseif $facture->_ref_last_consult->_date}}
      {{$facture->_ref_last_consult->_date|date_format:"%d/%m/%Y"}}
    {{else}}
      {{$facture->_ref_last_sejour->date|date_format:"%d/%m/%Y"}}
    {{/if}}
    </span>
  </td>
  <td  {{if $_acte_caisse->code}} class="acte-{{$_acte_caisse->_class}}">{{mb_value object=$_acte_caisse field="code"}}{{else}}>{{/if}}</td>
  <td style="white-space: pre-line;" class="compact">{{$_acte_caisse->_ref_prestation_caisse->libelle}}</td>
  <td style="text-align:right;">
    {{if $_acte_caisse->quantite}}
      {{$_acte_caisse->montant_base/$_acte_caisse->quantite|string_format:"%0.2f"}}
    {{else}}
      {{$_acte_caisse->montant_base|string_format:"%0.2f"}}
    {{/if}}
  </td>
  <td style="text-align:right;">{{mb_value object=$_acte_caisse field="quantite"}}</td>
  <td style="text-align:right;">{{$coeff_caisse}}
  </td>
  <td style="text-align:right;">{{$_acte_caisse->montant_base*$coeff_caisse|string_format:"%0.2f"|currency}}</td>
</tr>