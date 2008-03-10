<table class="tbl" id="order[{{$order->_id}}]">
  <tr>
    <th colspan="6">{{$order->_view}}</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>PU</th>
    <th>Quantité</th>
    <th>Prix</th>
    <th></th>
  </tr>
  <tbody>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    {{include file="inc_vw_order_item.tpl"}}
  {{/foreach}}
  </tbody>
  <tr>
    <td colspan="6" id="order[{{$order->_id}}][total]" style="border-top: 1px solid #666; text-align: right;">Total : {{$order->_total|string_format:"%.2f"}}</td>
  </tr>
</table>
