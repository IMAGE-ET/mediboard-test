{{foreach from=$orders item=curr_order}}
  <tr id="order[{{$curr_order->_id}}]">
    <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
    <td>{{$curr_order->_ref_societe->_view}}</td>
    <td>{{$curr_order->_ref_order_items|@count}}</td>
    <td>{{$curr_order->date_ordered|date_format:"%d/%m/%Y"}}</td>
    <td>{{$curr_order->_date_received|date_format:"%d/%m/%Y"}}</td>
    <td>{{$curr_order->_total|string_format:"%.2f"}}</td>
    <td>redo del</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="8">Aucune commande</td>
  </tr>
{{/foreach}}