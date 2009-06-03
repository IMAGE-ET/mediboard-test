<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        &mdash; Dr {{$prat->_view}} &mdash;<br />
        Plages du {{$filter->_date_min|date_format:"%A %d %b %Y"}}
        au {{$filter->_date_max|date_format:$dPconfig.longdate}}<br />
        {{$plages|@count}}
        {{if $filter->paye}}
        plage(s) payée(s)
        {{else}}
        plage(s) en attente de paiement
        {{/if}}
        sur la periode
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Date</th>
          <th>Plage horaire</th>
          <th>Libellé</th>
          <th>Tarif</th>
        </tr>
        {{foreach from=$plages item=curr_plage}}
        <tr>
          <td>{{$curr_plage->date|date_format:$dPconfig.longdate}}</td>
          <td>
            {{$curr_plage->debut|date_format:$dPconfig.time}}
            &mdash;
            {{$curr_plage->fin|date_format:$dPconfig.time}}
          </td>
          <td>{{$curr_plage->libelle}}</td>
          <td>{{$curr_plage->tarif}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        {{/foreach}}
        <tr>
          <th colspan="2" />
          <th>Total</th>
          <td><strong>{{$total}} {{$dPconfig.currency_symbol}}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
</table>