<table class="tbl">
  <tr>
    {{if !$order->date_ordered}}<th style="width: 1%;"></th>{{/if}}
    <th>{{tr}}CProductOrderItem-reference_id{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-quantity{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-unit_price{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-_price{{/tr}}</th>
    {{if $order->date_ordered}}<th></th>{{/if}}
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
    {{include file="inc_order_item.tpl"}}
    </tbody>
  {{/foreach}}
  <tr>
    <td colspan="6" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <span style="float: right;">Total : {{mb_value object=$order field=_total}}</span>
      <button type="button" class="change" onclick="refreshOrder({{$order->_id}}, {refreshLists: true})">{{tr}}Refresh{{/tr}}</button>
      
      {{if !$order->date_ordered && $order->_ref_order_items|@count > 0}}
       <form name="order-lock-{{$order->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="locked" value="1" />
        <button type="button" class="tick" onclick="if (confirmLock()) submitOrder(this.form, {close: true});">{{tr}}CProductOrder-locked{{/tr}}</button>
      </form>
      {{/if}}
    </td>
  </tr>
</table>
