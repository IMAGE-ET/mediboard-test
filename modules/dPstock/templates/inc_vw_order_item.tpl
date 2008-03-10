<tr id="order[{{$order->_id}}][{{$curr_item->_id}}]">
  <td>
    <form name="form-item-del-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="del" value="0" />
      <button type="button" class="trash notext" onclick="confirmDeletion(this.form,{typeName:'l\article',objName:'{{$curr_item->_view|smarty:nodefaults|JSAttribute}}', ajax: 1 }, {onComplete: function() {refreshOrder({{$order->_id}}) } })"></button>
    </form>
    {{$curr_item->_view}}
  </td>
  <td>{{$curr_item->unit_price|string_format:"%.2f"}}</td>
  <td>
    <form name="form-item-dec-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="quantity" value="{{$curr_item->quantity-1}}" />
      <button type="button" class="remove notext" onclick="submitOrderItem(this.form, {{$order->_id}})"></button>
    </form>
    {{$curr_item->quantity}}
    <form name="form-item-inc-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="quantity" value="{{$curr_item->quantity+1}}" />
      <button type="button" class="add notext" onclick="submitOrderItem(this.form, {{$order->_id}})"></button>
    </form>
  </td>
  <td>{{$curr_item->_price|string_format:"%.2f"}}</td>
</tr>
