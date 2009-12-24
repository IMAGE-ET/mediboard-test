{{* $Id: inc_order_item.tpl 7667 2009-12-18 16:49:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  {{assign var=order_id value=$curr_item->order_id}}
  {{assign var=id value=$curr_item->_id}}
  <td colspan="6">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$curr_item->_ref_reference->_guid}}')">
      {{$curr_item->_view|truncate:80}}
    </strong>
  </td>
</tr>
<tr>
  <td />
  <td>
    {{mb_value object=$curr_item field=quantity}}
  </td>
  <td>{{mb_value object=$curr_item field=unit_price}}</td>
  <td id="order-item-{{$id}}-price">{{mb_value object=$curr_item field=_price}}</td>
  
  <td>{{$curr_item->_quantity_received}}</td>
  
  <!-- Receive item -->
  <td style="padding: 0;">
    <form name="form-item-receive-{{$curr_item->_id}}" action="?" method="post" onsubmit="return makeReception(this, '{{$order->_id}}')">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
      <input type="hidden" name="order_item_id" value="{{$curr_item->_id}}" />
      <input type="hidden" name="date" value="now" />
      
      <table style="border-collapse: collapse; border-spacing: 0;">
        {{* <tr>
          <th>{{tr}}CProductOrderItemReception-quantity-court{{/tr}}</th>
          <th>{{tr}}CProductOrderItemReception-code{{/tr}}</th>
          <th>{{tr}}CProductOrderItemReception-lapsing_date-court{{/tr}}</th>
          <th></th>
        </tr> *}}
        {{foreach from=$curr_item->_ref_receptions item=curr_reception}}
        <tr title="{{mb_value object=$curr_reception field=date}}">
          <td>{{mb_value object=$curr_reception field=quantity}}</td>
          <td>{{$curr_reception->code}}</td>
          <td>{{mb_value object=$curr_reception field=lapsing_date}}</td>
          <td>
            <button type="button" class="cancel notext" onclick="cancelReception({{$curr_reception->_id}}, function() {refreshOrder({{$order->_id}})})">{{tr}}Cancel{{/tr}}</button>
            <input type="checkbox" name="barcode_printed" {{if $curr_reception->barcode_printed == 1}}checked="checked"{{/if}} 
                   onclick="barcodePrintedReception({{$curr_reception->_id}},this.checked)" 
                   title="{{tr}}CProductOrderItemReception-barcode_printed-court{{/tr}}" />
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td>
            {{mb_field 
              object=$curr_item 
              field=quantity
              form=form-item-receive-$id 
              increment=true
              size=2
              min=0
              style="width: 2em;"
              value=$curr_item->quantity-$curr_item->_quantity_received
            }}
          </td>
          <td>
            <input type="text" name="code" value="" size="6" title="{{tr}}CProductOrderItemReception-code{{/tr}}" />
          </td>
          <td>
            <input type="text" name="lapsing_date" value="" size="10" class="date mask|99/99/9999 format|$3-$2-$1" title="{{tr}}CProductOrderItemReception-lapsing_date{{/tr}}" />
          </td>
          <td>
            <!--<button type="submit" class="tick notext">{{tr}}CProductOrderItem-_receive{{/tr}}</button>-->
          </td>
         </tr>
       </table>
    </form>
  </td>
</tr>