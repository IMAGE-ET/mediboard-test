{{foreach from=$orders item=curr_order}}
  <tr id="order[{{$curr_order->_id}}]">
    <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
    <td>{{$curr_order->_ref_societe->_view}}</td>
    <td>
      <a class="buttonedit" href="?m={{$m}}&tab=vw_aed_order&order_id={{$curr_order->_id}}">
        {{$curr_order->_ref_order_items|@count}}
      </a>
    </td>
    <td>{{$curr_order->_total|string_format:"%.2f"}}</td>
    <td>
      <form name="order-lock-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
        <input type="hidden" name="locked" value="{{if $curr_order->locked}}0{{else}}1{{/if}}" />
        <button type="button" class="lock" onclick="submitOrder(this.form)">
          {{if $curr_order->locked}}Oui{{else}}Non{{/if}}
        </button>
      </form>
      
    </td>
    <td>
      <form name="order-order-{{$curr_item->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
        <input type="hidden" name="date_ordered" value="now" />
        <button type="button" class="tick" onclick="submitOrder(this.form)">Commander</button>
      </form>
  </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="8">Aucune commande</td>
  </tr>
{{/foreach}}