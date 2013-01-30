{{if !$conf.dPccam.CCodeCCAM.use_cotation_ccam}}
  {{mb_return}}
{{/if}}

<tr>
  <th class="category narrow" class="">Date</th>
  <th class="category">Code</th>
  <th class="category">Libelle</th>
  <th class="category narrow">Base</th>
  <th class="category narrow">DH</th>
  <th class="category narrow">Montant</th>
</tr>

{{foreach from=$facture->_ref_consults item=_consultation}}

  {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
    {{foreach from=$_consultation->_ref_actes_ccam item=_acte_ccam key="_key" name="tab"}}
      {{assign var=key value=$smarty.foreach.tab.index}}
      <tr>
        <td>{{mb_value object=$_consultation field=_date}}</td>
        <td class="acte-{{$_acte_ccam->_class}}">{{$_acte_ccam->code_acte}}</td>
        <td>{{$_consultation->_ext_codes_ccam.$key->libelleLong|truncate:70:"...":true}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ccam field=montant_base}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ccam field=montant_depassement}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ccam field=_montant_facture}}</td>
      </tr>
    {{/foreach}}
    
    {{foreach from=$_consultation->_ref_actes_ngap item=_acte_ngap}}
      <tr>
        <td>{{mb_value object=$_consultation field=_date}}</td>
        <td class="acte-{{$_acte_ngap->_class}}">{{$_acte_ngap->code}}</td>
        <td>{{$_acte_ngap->_libelle}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ngap field="montant_base"}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ngap field=_montant_facture}}</td>
      </tr>
    {{/foreach}}
  {{/if}}

  <tbody class="hoverable">
    <tr>
      <td colspan="3" rowspan="4"></td>
      <td colspan="2">Dû Patient</td>
      <td style="text-align:right;">{{mb_value object=$facture field="du_patient"}}</td>
    </tr>
    <tr>
      <td colspan="2">Dû Tiers</td>
      <td style="text-align:right;">{{mb_value object=$facture field="du_tiers"}}</td>
    </tr>
    <tr>
      <td colspan="2"><b>Montant Total</b></td>
      <td style="text-align:right;"><b>{{mb_value object=$facture field="_montant_avec_remise"}}</b></td>
    <tr>
  </tbody>
  
{{foreachelse}}
  <tr><td colspan="10" class="empty">{{tr}}CConsultation.none{{/tr}}</td></tr>
{{/foreach}}