{{if $type=="waiting"}}
<table class="tbl">
  <tr>
    <th>Numéro</th>
    <th>Fournisseur</th>
    <th>Articles</th>
    <th>Total</th>
    <th>Bloquée</th>
    <th>Actions</th>
  </tr>
  <tbody>
	{{foreach from=$orders item=curr_order}}
	  <tr id="order-{{$curr_order->_id}}">
	    <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{$curr_order->order_number}}</a></td>
	    <td>{{$curr_order->_ref_societe->_view}}</td>
	    <td>
	      {{$curr_order->_ref_order_items|@count}}
        <button class="edit" onclick="popupOrder({{$curr_order->_id}}, 800, 600); return false;">Editer</button>
	    </td>
	    <td>{{mb_value object=$curr_order field=_total}}</td>
	    <td>
	      <form name="order-lock-{{$curr_order->_id}}" action="?" method="post">
	        <input type="hidden" name="m" value="{{$m}}" />
	        <input type="hidden" name="dosql" value="do_order_aed" />
	        <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
	        {{mb_field object=$curr_order field="locked" typeEnum="checkbox" onChange="submitOrder(this.form)"}}
	      </form>
	    </td>
	    <td>
      {{if $curr_order->_ref_order_items|@count > 0}}
	      <form name="order-order-{{$curr_order->_id}}" action="?" method="post">
	        <input type="hidden" name="m" value="{{$m}}" />
	        <input type="hidden" name="dosql" value="do_order_aed" />
	        <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
	        <input type="hidden" name="_order" value="1" />
	        <button type="button" class="tick" onclick="submitOrder(this.form, true)">Commander</button>
	      </form>
        {{/if}}
	  </td>
	  </tr>
	{{foreachelse}}
	  <tr>
	    <td colspan="8">Aucune commande</td>
	  </tr>
	{{/foreach}}
  
  </tbody>
</table>
{{elseif $type=="pending"}}
<table class="tbl">
  <tr>
    <th>Numéro</th>
    <th>Fournisseur</th>
    <th>Articles/Reçus</th>
    <th>Passée le</th>
    <th>Total</th>
    <th>Actions</th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr id="order-{{$curr_order->_id}}">
      <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{$curr_order->order_number}}</a></td>
      <td>{{$curr_order->_ref_societe->_view}}</td>
      <td>{{$curr_order->_ref_order_items|@count}}/{{$curr_order->_count_received}}</td>
      <td>{{mb_value object=$curr_order field=date_ordered}}</td>
      <td>{{mb_value object=$curr_order field=_total}}</td>
      <td><button class="tick" onclick="popupOrder({{$curr_order->_id}});">Recevoir</button></td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">Aucune commande</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>
{{else}}
<table class="tbl">
  <tr>
    <th>Numéro</th>
    <th>Fournisseur</th>
    <th>Articles</th>
    <th>Passée le</th>
    <th>Reçue le</th>
    <th>Total</th>
    <th>Actions</th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr id="order-{{$curr_order->_id}}">
      <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{$curr_order->order_number}}</a></td>
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
{{/if}}

<!-- The orders count -->
<script type="text/javascript">
  $('orders-{{$type}}-count').innerHTML = {{$orders|@count}};
</script>