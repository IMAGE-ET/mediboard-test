{{mb_include_script module=dPstock script=order_manager}}

<form name="order-order-{{$order->_id}}" action="?" method="post">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_order_aed" />
  <input type="hidden" name="order_id" value="{{$order->_id}}" />
  <input type="hidden" name="_order" value="1" />
  <button class="print" onclick="window.print(); submitOrder(this.form, {close: true});">Imprimer</button>
</form>

<table class="tbl">
  <tr>
    <th>{{tr}}CProduct-name{{/tr}}</th>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-quantity{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-unit_price{{/tr}}</th>
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
    {{include file="inc_order_form_item.tpl"}}
    </tbody>
  {{/foreach}}
  <tr>
    <td colspan="6" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <span style="float: right;">Total : {{mb_value object=$order field=_total}}</span>
    </td>
  </tr>
</table>