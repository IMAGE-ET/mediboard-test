<tr>
  <td>{{$curr_item->_view}}</td>
  <td>{{mb_value object=$curr_item field=unit_price}}</td>
  <td>
    <form name="form-item-dec-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="quantity" value="{{$curr_item->quantity-1}}" />
      <button type="button" class="remove notext" onclick="submitOrderItem(this.form, {{$curr_item->_id}})">-1</button>
    </form>
    {{$curr_item->quantity}}
    <form name="form-item-inc-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="quantity" value="{{$curr_item->quantity+1}}" />
      <button type="button" class="add notext" onclick="submitOrderItem(this.form, {{$curr_item->_id}})">+1</button>
    </form>
  </td>
  <td>{{mb_value object=$curr_item field=_price}}</td>
  <td>
    <form name="form-item-received-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      {{if $curr_item->date_received}}
      <input type="hidden" name="_receive" value="0" />
      <button type="button" class="tick" onclick="submitOrderItem(this.form, {{$curr_item->_id}})">Annuler</button>
      {{else}}
      <input type="hidden" name="_receive" value="1" />
      <button type="button" class="cancel" onclick="submitOrderItem(this.form, {{$curr_item->_id}})">Recevoir</button>
      {{/if}}
    </form>
  </td>
</tr>
