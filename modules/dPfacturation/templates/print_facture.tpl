<html>
  <body>
  <style type="text/css">
    {{$style|smarty:nodefaults}}
    @media print {
      div.body {
        padding-top: {{$header_height}}px;
        padding-bottom: {{$footer_height}}px;
        height: {{$body_height}}px;
      }
      th.title {
        font-size: 0.8em;
      }
      th.text {
        font-size: 0.8em;
      }
      td {
        font-size: 0.8em;
      }
      div.header {
        height: {{$header_height}}px;
      }
      div.footer {
        height: {{$footer_height}}px;
      }
    }
  </style>

    <div class="header">
      {{if $header}}
        {{$header|smarty:nodefaults}}
      {{else}}
        <table class="form">
          <tr>
            <th class="category">Facture</th>
            <th style="text-align: left;">{{$facture->_view}}</th>
            <th>Créée le</th>
            <th style="text-align: center;">{{mb_value object=$facture field=ouverture}}</th>
          </tr>
          <tr>
            <th class="category">Praticien</th>
            <th style="text-align: left;">{{$facture->_ref_praticien}}</th>
            {{if $facture->_ref_praticien->rpps}}
              <th>{{mb_title object=$facture->_ref_praticien field=rpps}}</th>
              <th>{{mb_value object=$facture->_ref_praticien field=rpps}}</th>
             {{elseif $facture->_ref_praticien->adeli}}
              <th>{{mb_title object=$facture->_ref_praticien field=adeli}}</th>
              <th>{{mb_value object=$facture->_ref_praticien field=adeli}}</th>
            {{else}}
              <th colspan="2"></th>
            {{/if}}
          </tr>
          <tr>
            <th class="category">Patient</th>
            <th style="text-align: left;">{{$facture->_ref_patient}}</th>
            <th>Date de naissance</th>
            <th style="text-align: center;">{{mb_value object=$facture->_ref_patient field=naissance}}</th>
          </tr>
        </table>
      {{/if}}
    </div>
    <div class="body">
      <table style="width: 100%;" class="form">
        <tr>
          <th class="category" colspan="6">Actes réalisés</th>
        </tr>
        <tr>
          <th class="category narrow">Date</th>
          <th class="category narrow">Code</th>
          <th class="category">Libellé</th>
          <th class="category narrow">Base</th>
          <th class="category narrow">DH</th>
          <th class="category narrow">Montant</th>
        </tr>
        {{foreach from=$facture->_ref_items item=item}}
          <tr>
            <td>{{mb_value object=$item field="date"}}</td>
            <td>{{$item->code}}</td>
            <td>{{$item->libelle|truncate:60:"...":true}}</td>
            <td style="text-align: right;">{{$item->montant_base|string_format:"%0.2f"}}</td>
            <td style="text-align: right;">{{$item->montant_depassement|string_format:"%0.2f"}}</td>
            <td style="text-align: right;">{{$item->_montant_facture|string_format:"%0.2f"|currency}}</td>
          </tr>
        {{/foreach}}
        <tr>
          <td colspan="3"></td>
          <td colspan="2">Dû Patient</td>
          <td style="text-align: right;">{{mb_value object=$facture field=du_patient}}</td>
        </tr>
        <tr>
          <td colspan="3"></td>
          <td colspan="2">Dû Tiers</td>
          <td style="text-align: right;">{{mb_value object=$facture field=du_tiers}}</td>
        </tr>
        <tr>
          <td colspan="3"></td>
          <td colspan="2"><i>Dont TVA ({{$facture->taux_tva}}%)</i></td>
          <td style="text-align: right;"><i>{{mb_value object=$facture field=du_tva}}</i></td>
        </tr>
        <tr>
          <td colspan="3"></td>
          <td colspan="2"><b>Montant Total</b></td>
          <td style="text-align: right;">{{mb_value object=$facture field=_montant_avec_remise}}</td>
        </tr>
      </table>
      <table style="width: 100%;top: 550px;position: absolute;" class="form">
        <tr>
          <th>Nombre de règlements déjà effectués</th>
          <td style="text-align: center;">{{$facture->_ref_reglements_patient|@count}}</td>
        </tr>
        <tr>
          <th>Total des règlements patient</th>
          <td style="text-align: right;">{{mb_value object=$facture field=_reglements_total_patient}}</td>
        </tr>
        <tr>
          <th>Montant restant à payer</th>
          <th><strong>{{mb_value object=$facture field=_du_restant_patient}}</strong></th>
        </tr>
      </table>
    </div>

    <div class="footer">
      {{if $footer}}
        {{$footer|smarty:nodefaults}}
      {{else}}
        <table class="form">
          <tr>
            <td>Veuillez effectuer un règlement du montant indiqué ci dessus ({{mb_value object=$facture field=du_patient}}).</td>
          </tr>
          <tr>
            <td>La validité de cette facture est de 30 jours.</td>
          </tr>
        </table>
      {{/if}}
    </div>
  </body>
</html>