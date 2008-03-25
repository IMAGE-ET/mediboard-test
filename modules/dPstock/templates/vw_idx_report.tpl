<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
      <tr>
        <th style="width: 40px;">Actuel</th>
        <th style="width: 40px;">Futur</th>
        <th>Produit</th>
        <th>En stock</th>
        <th>Niveaux</th>
        <th>Quantité en commande</th>
        <th>Date commande en cours</th>
      </tr>
      {{foreach from=$list_stocks item=curr_stock}}
        <tr>
          {{assign var=current value=$curr_stock->_zone}}
          {{assign var=future value=$curr_stock->_zone_future}}
          <td style="background: {{$colors.$current}}"></td>
          <td style="background: {{$colors.$future}}"></td>
          <td><a href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->_id}}" title="Voir ou modifier le stock">{{$curr_stock->_view}}</a></td>
          <td>{{mb_value object=$curr_stock field=quantity}}</td>
          <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
          <td>{{mb_value object=$curr_stock field=_ordered_count}}</td>
          <td>{{mb_value object=$curr_stock field=_ordered_last}}</td>
        </tr>
      {{/foreach}}
      </table>
    </td>
  </tr>
</table>