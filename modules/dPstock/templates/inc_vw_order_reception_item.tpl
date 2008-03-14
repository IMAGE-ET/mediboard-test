<tr>
  <td>{{$curr_item->_view}}</td>
  <td>{{mb_value object=$curr_item field=unit_price}}</td>
  <td>
    {{$curr_item->quantity}}
  </td>
  <td>{{mb_value object=$curr_item field=_price}}</td>
  <td>
    {{if $curr_item->date_received}}
      Oui
    {{else}}
    <form name="form-item-received-{{$curr_item->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="_receive" value="1" />
      <button type="button" class="tick" onclick="submitOrderItem(this.form, {{$curr_item->_id}})">Recevoir</button>
    </form>
    {{/if}}
  </td>
</tr>
