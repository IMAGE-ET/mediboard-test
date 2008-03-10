{{foreach from=$orders item=curr_order}}
  <tr id="order[{{$curr_order->_id}}]">
    <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
    <td>{{$curr_order->_ref_societe->_view}}</td>
    <td>{{$curr_order->_ref_order_items|@count}}/{{$curr_order->_received}}</td>
    <td>{{mb_value object=$curr_order field=date_ordered}}</td>
    <td>{{$curr_order->_total|string_format:"%.2f"}}</td>
    <td>actions</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="8">Aucune commande</td>
  </tr>
{{/foreach}}