<table class="tbl">
  <tr>
    <th>Produit</th>
    <th>Quantité</th>
    <th>PU</th>
    <th>Prix</th>
    {{if $order->date_ordered}}
    <th style="width: 1%">Reçu</th>
    {{/if}}
  </tr>
  <tbody>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    {{include file="inc_order_item.tpl"}}
  {{/foreach}}
  </tbody>
  <tr>
    <td colspan="6" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
    <span style="float: right;">Total : {{mb_value object=$order field=_total}}</span>
    {{if (!$order->date_ordered) && ($order->_ref_order_items|@count > 0)}}
      <form name="order-order-{{$order->_id}}" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_order_manager" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="_order" value="1" />
        <button type="submit" class="tick">Commander</button>
      </form>
    {{/if}}
    </td>
  </tr>
</table>
