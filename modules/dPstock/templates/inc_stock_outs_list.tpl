<table class="tbl">
  <tr>
    <th colspan="5" class="title">20 derniers déstockages</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>Date</th>
    <th>Quantité</th>
    <th>Code produit</th>
    <th>Pour</th>
  </tr>
  {{foreach from=$list_latest_stock_outs item=curr_stock_out}}
  <tr>
    <td>{{$curr_stock_out->_ref_stock->_view}}</td>
    <td class="date">{{mb_value object=$curr_stock_out field=date}}</td>
    <td>{{mb_value object=$curr_stock_out field=quantity}}</td>
    <td>{{mb_value object=$curr_stock_out field=product_code}}</td>
    <td>{{mb_value object=$curr_stock_out->_ref_function field=_view}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">Autun déstockage</td>
  </tr>
  {{/foreach}}
</table>
