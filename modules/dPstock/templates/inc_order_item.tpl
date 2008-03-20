<tr>
  <td>
    {{if !$order->date_ordered}}
    <!-- Delete order item -->
    <form name="form-item-del-{{$curr_item->_id}}" action="?" method="post" style="display: inline;">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="del" value="0" />
      <button type="button" class="trash notext" onclick="confirmDeletion(this.form,{typeName:'l\article',objName:'{{$curr_item->_view|smarty:nodefaults|JSAttribute}}', ajax: 1 }, {onComplete: function() {refreshOrder({{$order->_id}}) } })"></button>
    </form>
    {{/if}}
    <a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_item->_ref_reference->_ref_product->_id}}">{{$curr_item->_view}}</a>
  </td>
  <td style="white-space: nowrap;">
    {{if !$order->date_ordered}}
    <!-- Order item quantity change -->
    <form name="form-item-quantity-{{$curr_item->_id}}" action="?" method="post">
      <button type="button" class="remove notext" onclick="this.form.quantity.value--; submitOrderItem(this.form);"></button>
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="text" name="quantity" value="{{$curr_item->quantity}}" size="2" onchange="submitOrderItem(this.form);" />
      <button type="button" class="add notext" onclick="this.form.quantity.value++; submitOrderItem(this.form);"></button>
    </form>
    {{else}}
      {{mb_value object=$curr_item field=quantity}}
    {{/if}}
  </td>
  <td>{{mb_value object=$curr_item field=unit_price}}</td>
  <td>{{mb_value object=$curr_item field=_price}}</td>
  
  {{if $order->date_ordered}}
  <!-- Receive item -->
  <td>
    {{if $curr_item->date_received}}
    <form name="form-item-receive-{{$curr_item->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="_receive" value="-1" />
      <button type="button" class="cancel" onclick="submitOrderItem(this.form)">Annuler</button>
    </form>
    {{else}}
    <form name="form-item-receive-{{$curr_item->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="_receive" value="1" />
      <button type="button" class="tick" onclick="submitOrderItem(this.form)">Recevoir</button>
    </form>
    {{/if}}
  </td>
  {{/if}}
</tr>