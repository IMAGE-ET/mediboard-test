<table class="tbl">
  <tr>
    <th class="title" colspan="10">Commande chez {{$order->_ref_societe->_view}}</th>
  </tr>
  <tr>
    {{if !$order->date_ordered}}
    <th style="width: 1%;"></th>
    {{/if}}
    <th>Produit</th>
    <th>Quantité</th>
    <th>PU</th>
    <th>Prix</th>
    {{if $order->date_ordered}}
    <th>Reçu</th>
    {{/if}}
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
    {{include file="inc_order_item.tpl"}}
    </tbody>
  {{/foreach}}
  <tr>
    <td colspan="6" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <span style="float: right;">Total : {{mb_value object=$order field=_total}}</span>
      <button type="button" class="change" onclick="refreshOrder({{$order->_id}}, true)">Rafraichir</button>
      
      {{if !$order->date_ordered}}
       <form name="order-{{$order->_id}}-order" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="_order" value="1" />
        <button type="button" class="tick" onclick="submitOrder(this.form, {{$order->_id}});">Commander</button>
      </form>
      {{/if}}
    </td>
  </tr>
</table>
