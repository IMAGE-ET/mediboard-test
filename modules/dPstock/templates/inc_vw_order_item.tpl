<tr id="order[{{$order->_id}}][{{$curr_item->_id}}]">
  <td>{{$curr_item->_view}}</td>
  <td>{{$curr_item->unit_price|string_format:"%.2f"}}</td>
  <td>{{$curr_item->quantity}}</td>
  <td>{{$curr_item->_price|string_format:"%.2f"}}</td>
  <td><a href="#1" onclick="actionOrderItem({{$order->_id}}, 'delete', {{$curr_item->_id}})">del</a>
  <a href="#1" onclick="actionOrderItem({{$order->_id}}, 'inc', {{$curr_item->_id}})"> + </a>
  <a href="#1" onclick="actionOrderItem({{$order->_id}}, 'dec', {{$curr_item->_id}})"> - </a></td>
</tr>
