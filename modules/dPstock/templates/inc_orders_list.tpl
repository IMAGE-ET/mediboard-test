{{if $type=="waiting"}}
<!-- Orders not validated yet -->
<table class="tbl">
  <tr>
    <th>{{tr}}CProductOrder-order_number{{/tr}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-_ref_order_items{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
	{{foreach from=$orders item=curr_order}}
	  <tr>
	    <td>{{$curr_order->order_number}}</td>
	    <td>{{$curr_order->_ref_societe->_view}}</td>
	    <td>{{$curr_order->_ref_order_items|@count}}</td>
	    <td class="currency">{{mb_value object=$curr_order field=_total}}</td>
	    <td>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="1" />
          <button type="button" class="edit" onclick="popupOrder(this.form, 800, 600); return false;">{{tr}}Modify{{/tr}}</button>
          <button type="button" class="cancel" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}Cancel{{/tr}}</button>
        </form>
        {{if $curr_order->_ref_order_items|@count > 0}}
        <form name="order-lock-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="locked" value="1" />
          <button type="button" class="tick" onclick="if (confirmLock()) submitOrder(this.form, {refreshLists: true});">{{tr}}CProductOrder-locked{{/tr}}</button>
        </form>
        {{/if}}
	   </td>
	  </tr>
	{{foreachelse}}
	  <tr>
	    <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
	  </tr>
	{{/foreach}}
  </tbody>
</table>


{{elseif $type=="locked"}}
<!-- Orders locked -->
<table class="tbl">
  <tr>
    <th>{{tr}}CProductOrder-order_number{{/tr}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-_ref_order_items{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>{{$curr_order->order_number}}</td>
      <td>{{$curr_order->_ref_societe->_view}}</td>
      <td>{{$curr_order->_ref_order_items|@count}}</td>
      <td class="currency">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="1" />
          <button type="button" class="cancel" onclick="submitOrder(this.form, {refreshLists: true})">Annuler</button>
        </form>
        <form name="order-order-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="_order" value="1" />
          <button type="button" class="tick" onclick="if (confirmOrder()) submitOrder(this.form, {refreshLists: true})">Commander</button>
        </form>
     </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>



{{elseif $type=="pending"}}
<!-- Orders not received yet -->
<table class="tbl">
  <tr>
    <th>{{tr}}CProductOrder-order_number{{/tr}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-_ref_order_items{{/tr}} / {{tr}}CProductOrder-_count_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>{{$curr_order->order_number}}</td>
      <td>{{$curr_order->_ref_societe->_view}}</td>
      <td>{{$curr_order->_ref_order_items|@count}}/{{$curr_order->_count_received}}</td>
      <td class="date">{{mb_value object=$curr_order field=date_ordered}}</td>
      <td class="currency">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="1" />
          <button type="button" class="cancel" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}Cancel{{/tr}}</button>
          <button type="button" class="tick" onclick="popupOrder(this.form);">{{tr}}CProductOrder-_receive{{/tr}}</button>
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>



{{elseif $type=="received"}}
<!-- Received orders -->
<table class="tbl">
  <tr>
    <th>{{tr}}CProductOrder-order_number{{/tr}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-_ref_order_items{{/tr}} / {{tr}}CProductOrder-_count_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_date_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>{{$curr_order->order_number}}</td>
      <td>{{$curr_order->_ref_societe->_view}}</td>
      <td>{{$curr_order->_ref_order_items|@count}}</td>
      <td class="date">{{mb_value object=$curr_order field=date_ordered}}</td>
      <td class="date">{{mb_value object=$curr_order field=_date_received}}</td>
      <td class="currency">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="_redo" value="1" />
          <button type="button" class="change" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}CProductOrder-_redo{{/tr}}</button>
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>



{{else}}
<!-- Cancelled orders -->
<table class="tbl">
  <tr>
    <th>{{tr}}CProductOrder-order_number{{/tr}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    <th>{{tr}}CProductOrder-_ref_order_items{{/tr}}</th>
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_date_received{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th style="width: 1%;"></th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr>
      <td>{{$curr_order->order_number}}</td>
      <td>{{$curr_order->_ref_societe->_view}}</td>
      <td>{{$curr_order->_ref_order_items|@count}}</td>
      <td class="date">{{mb_value object=$curr_order field=date_ordered}}</td>
      <td class="date">{{mb_value object=$curr_order field=_date_received}}</td>
      <td class="currency">{{mb_value object=$curr_order field=_total}}</td>
      <td>
        <form name="order-cancel-{{$curr_order->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_aed" />
          <input type="hidden" name="order_id" value="{{$curr_order->_id}}" />
          <input type="hidden" name="cancelled" value="0" />
          <button type="button" class="tick" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}Restore{{/tr}}</button>
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="8">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>
{{/if}}

<!-- The orders count -->
<script type="text/javascript">
  $('list-orders-{{$type}}-count').innerHTML = {{$orders|@count}};
</script>