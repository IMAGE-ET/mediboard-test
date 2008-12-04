{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<table class="tbl">
  <tr>
    {{if !$order->date_ordered}}<th style="width: 1%;"></th>{{/if}}
    <th>{{tr}}CProductOrderItem-reference_id{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-quantity{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-unit_price{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-_price{{/tr}}</th>
    {{if $order->date_ordered}}<th>Déjà reçu</th><th></th>{{/if}}
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
    {{include file="inc_order_item.tpl"}}
    </tbody>
  {{/foreach}}
  <tr>
    <td colspan="8" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <span style="float: right;">{{tr}}Total{{/tr}} : <span id="order-total">{{mb_value object=$order field=_total}}</span></span>
      <button type="button" class="change" onclick="refreshOrder({{$order->_id}}, {refreshLists: true})">{{tr}}Refresh{{/tr}}</button>
      <button type="button" class="print" onclick="printBarcodeGrid('{{$order->_id}}')">Imprimer les codes barres</button>
      
      {{if !$order->date_ordered && $order->_ref_order_items|@count > 0}}
       <form name="order-lock-{{$order->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="locked" value="1" />
        <button type="button" class="tick" onclick="submitOrder(this.form, {close: true, confirm: true});">{{tr}}CProductOrder-_validate{{/tr}}</button>
      </form>
      {{/if}}
    </td>
  </tr>
</table>
