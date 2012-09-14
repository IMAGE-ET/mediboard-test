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
        <td style="background-color:#FF69B4;">{{$_acte_ccam->code_acte}}</td>
        <td>{{$_consultation->_ext_codes_ccam.$key->libelleLong|truncate:70:"...":true}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ccam field=montant_base}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ccam field=montant_depassement}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ccam field=_montant_facture}}</td>
      </tr>
    {{/foreach}}
    
    {{foreach from=$_consultation->_ref_actes_ngap item=_acte_ngap}}
      <tr>
        <td>{{mb_value object=$_consultation field=_date}}</td>
        <td style="background-color:#32CD32;">{{$_acte_ngap->code}}</td>
        <td>{{$_acte_ngap->_libelle}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ngap field="montant_base"}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
        <td style="text-align: right;">{{mb_value object=$_acte_ngap field=_montant_facture}}</td>
      </tr>
    {{/foreach}}
  {{/if}}

  <tbody class="hoverable">
    {{assign var="nb_montants" value=$facture->_montant_factures|@count }}
    {{if $nb_montants > 1}}
      {{foreach from=$facture->_montant_factures item=_montant key=key}}
        <tr>
          {{if $key == 0}}
          <td colspan="3" rowspan="{{$nb_montants+2}}"></td>
           {{/if}}
          <td colspan="2">Montant n°{{$key+1}}</td>
          <td style="text-align:right;">{{$_montant|string_format:"%0.2f"}}</td>
        </tr>
      {{/foreach}}
    {{/if}}
    
    <tr>
      <td colspan="3" rowspan="4"></td>
      <td colspan="2">Montant</td>
      <td style="text-align:right;">{{mb_value object=$facture field="_montant_sans_remise"}}</td>
    </tr>
          
    <tr>
      <td colspan="2"><b>Montant Total</b></td>
      <td style="text-align:right;"><b>{{mb_value object=$facture field="_montant_avec_remise"}}</b></td>
    <tr>

  </tbody>
  
{{foreachelse}}
  <tr><td colspan="10" class="empty">{{tr}}CConsultation.none{{/tr}}</td></tr>
{{/foreach}}

