<tr>
  {{if !$order->date_ordered}}
  <td>
    <!-- Delete order item -->
    <form name="form-item-del-{{$curr_item->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="del" value="0" />
      <button type="button" class="trash notext" style="display: inline;" onclick="confirmDeletion(this.form,{typeName:'l\article',objName:'{{$curr_item->_view|smarty:nodefaults|JSAttribute}}', ajax: 1 }, {onComplete: function() {refreshOrder({{$order->_id}}, true) } })"></button>
    </form>
  </td>
  {{/if}}
  <td>
    {{if $dialog}}
      {{$curr_item->_view}}
    {{else}}
      <a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_item->_ref_reference->_ref_product->_id}}">{{$curr_item->_view}}</a>
    {{/if}}
  </td>
  <td>
    {{if !$order->date_ordered}}
    <!-- Order item quantity change -->
    <form name="form-item-quantity-{{$curr_item->_id}}" action="?" method="post">
      {{if $ajax}}
      <script type="text/javascript">
          prepareForm(document.forms['form-item-quantity-{{$curr_item->_id}}']);
      </script>
      {{/if}}
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      {{assign var=id value=$curr_item->_id}}
      {{mb_field object=$curr_item 
        field=quantity 
        onchange="submitOrderItem(this.form);" 
        form=form-item-quantity-$id 
        increment=1}}
    </form>
    {{else}}
      {{mb_value object=$curr_item field=quantity}}
    {{/if}}
  </td>
  <td>{{mb_value object=$curr_item field=unit_price}}</td>
  <td>{{mb_value object=$curr_item field=_price}}</td>
  
  {{if $order->date_ordered}}
  <!-- Receive item -->
  <td style="width: 1%; white-space: nowrap;">
    <form name="form-item-receive-{{$curr_item->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      {{assign var=id value=$curr_item->_id}}
      {{mb_field 
        object=$curr_item 
        field=_quantity_received 
        onchange="submitOrderItem(this.form, 0, $id);" 
        form=form-item-receive-$id 
        increment=1
        max=$curr_item->quantity
      }}
      <button type="button" class="tick" onclick="this.form._quantity_received.value = {{$curr_item->quantity}}; submitOrderItem(this.form, 0, {{$curr_item->_id}});">Tout</button>
    </form>
  </td>
  {{/if}}
</tr>