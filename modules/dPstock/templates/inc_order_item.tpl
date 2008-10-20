{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<tr>
  {{if !$order->date_ordered}}
  <td>
    <!-- Delete order item -->
    <form name="form-item-del-{{$curr_item->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="del" value="0" />
      <button type="button" class="trash notext" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$curr_item->_view|smarty:nodefaults|JSAttribute}}', ajax: 1 }, {onComplete: function() {refreshOrder({{$order->_id}}, {refreshLists: true}) } })"></button>
    </form>
  </td>
  {{/if}}
  {{assign var=order_id value=$curr_item->order_id}}
  {{assign var=id value=$curr_item->_id}}
  <td>{{$curr_item->_view}}</td>
  <td>
    {{if !$order->date_ordered}}
    <!-- Order item quantity change -->
    <form name="form-item-quantity-{{$curr_item->_id}}" action="?" method="post">
      {{if $ajax}}
      <script type="text/javascript">
          prepareForm('form-item-quantity-{{$curr_item->_id}}');
      </script>
      {{/if}}
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      {{mb_field object=$curr_item 
        field=quantity 
        onchange="
          submitOrderItem(this.form, {noRefresh: true});
          refreshValue('order-total', 'CProductOrder', $order_id, '_total');
          refreshValue('order-item-$id-price', 'CProductOrderItem', $id, '_price');"
        form=form-item-quantity-$id 
        min=0
        size="3"
        increment=true}}
    </form>
    {{else}}
      {{mb_value object=$curr_item field=quantity}}
    {{/if}}
  </td>
  <td>{{mb_value object=$curr_item field=unit_price}}</td>
  <td id="order-item-{{$id}}-price">{{mb_value object=$curr_item field=_price}}</td>
  
  {{if $order->date_ordered}}
  <td style="width: 1%; white-space: nowrap;">{{$curr_item->_quantity_received}}</td>
  
  <!-- Receive item -->
  <td style="width: 1%; white-space: nowrap;">
    <form name="form-item-receive-{{$curr_item->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshOrder({{$order->_id}})})">
      {{if $ajax}}
      <script type="text/javascript">
          prepareForm('form-item-receive-{{$curr_item->_id}}');
      </script>
      {{/if}}
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="date" value="now" />
      {{mb_field 
        object=$curr_item 
        field=quantity
        form=form-item-receive-$id 
        increment=true
        size="3"
        max=$curr_item->quantity
        min=0
      }}
      <button type="submit" class="tick">{{tr}}CProductOrderItem-_receive{{/tr}}</button>
    </form>
  </td>
  {{/if}}
</tr>