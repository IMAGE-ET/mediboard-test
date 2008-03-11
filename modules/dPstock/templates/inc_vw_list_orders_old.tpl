<table class="tbl">
  <tr>
    <th>Intitulé</th>
    <th>Fournisseur</th>
    <th>Articles</th>
    <th>Passée le</th>
    <th>Reçue le</th>
    <th>Total</th>
    <th>Actions</th>
  </tr>
  <tbody>
	{{foreach from=$orders item=curr_order}}
	  <tr id="order[{{$curr_order->_id}}]">
	    <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
	    <td>{{$curr_order->_ref_societe->_view}}</td>
	    <td>{{$curr_order->_ref_order_items|@count}}</td>
	    <td>{{mb_value object=$curr_order field=date_ordered}}</td>
	    <td>{{*mb_value object=$curr_order field=date_received*}}</td>
	    <td>{{mb_value object=$curr_order field=_total}}</td>
	    <td>redo del</td>
	  </tr>
	{{foreachelse}}
	  <tr>
	    <td colspan="8">Aucune commande</td>
	  </tr>
	{{/foreach}}
	</tbody>
</table>