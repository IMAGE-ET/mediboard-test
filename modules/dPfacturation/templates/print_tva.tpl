<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport de TVA
              {{mb_include module=system template=inc_interval_date from=$date_min to=$date_max}}
            </a>
          </th>
        </tr>
        {{foreach from=$listPrat item=_prat}}
          <tr>
            <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>

    <td class="halfPane">
      <table class="tbl" style="width:400px;float:right;">
        <tr>
          <th class="title" colspan="3">Récapitulatif</th>
        </tr>
        <tr>
          <th class="narrow">Taux (en %)</th>
          <th class="narrow">Nombre de factures</th>
          <th>Total</th>
        </tr>
        {{foreach from=$list_taux item=taux}}
          <tr>
            <th>{{$taux|string_format:"%0.1f"}}</th>
            <td style="text-align:center;">{{$taux_factures.$taux.count}}</td>
            <td style="text-align:right;">{{$taux_factures.$taux.total|string_format:"%0.2f"|currency}}</td>
          </tr>
        {{/foreach}}
        <tr>
          <th>Total</th>
          <td style="text-align: center;"><b>{{$nb_factures}}</b></td>
          <td style="text-align:right;"><b>{{$total_tva|string_format:"%0.2f"|currency}}</b></td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Les factures-->
  {{foreach from=$list_taux item=taux}}
    {{if $taux_factures.$taux.factures|@count}}
      <tr>
        <td colspan="2"><strong>Taux à {{$taux}} %</strong></td>
      </tr>
      <tr>
        <td colspan="2">
          <table class="tbl">
            <tr>
              <th class="narrow text">{{tr}}CFactureCabinet{{/tr}}</th>
              <th style="width: 15%;">{{mb_label class=CConsultation field=_prat_id}}</th>
              <th style="width: 15%;">{{mb_label class=CConsultation field=patient_id}}</th>
              <th style="width: 15%;">{{mb_label class=CConsultation field=_date}}</th>
              <th style="width: 15%;">{{mb_label class=CConsultation field=secteur3}}</th>
              <th>TVA ({{$taux}} %)</th>
              <th>Facture HT</th>
              <th>Facture TTC</th>
            </tr>
            {{foreach from=$taux_factures.$taux.factures item=facture}}
              <tr>
                <td> <strong onmouseover="ObjectTooltip.createEx(this, '{{$facture->_guid}}')"> {{$facture}} </strong> </td>
                <td> {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$facture->_ref_praticien}} </td>
                <td>
                  {{assign var=patient value=$facture->_ref_patient}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
                </td>
                <td>{{mb_value object=$facture field=ouverture}}</td>
                <td style="text-align: right;">{{mb_value object=$facture field=_secteur3 format=currency}}</td>
                <td style="text-align: right;">{{mb_value object=$facture field=du_tva format=currency}}</td>
                <td style="text-align: right;">{{$facture->_montant_avec_remise-$facture->du_tva|currency}}</td>
                <td style="text-align: right;">{{mb_value object=$facture field=_montant_avec_remise format=currency}}</td>
              </tr>
            {{/foreach}}
            <tr style="text-align: right;">
              <td colspan="4"> <strong>{{tr}}Total{{/tr}}</strong> </td>
              <td><strong>{{$taux_factures.$taux.totalst|currency}} </strong></td>
              <td><strong>{{$taux_factures.$taux.total|currency}}   </strong></td>
              <td><strong>{{$taux_factures.$taux.totalht|currency}} </strong></td>
              <td><strong>{{$taux_factures.$taux.totalttc|currency}}</strong></td>
            </tr>
          </table>
        </td>
      </tr>
    {{/if}}
  {{/foreach}}
</table>