{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
popupOrderForm = function(order_id, width, height) {
  width = width || 1000;
  height = height || 800;

  var url = new Url("dPstock", "vw_order_form");
  url.addParam("order_id", order_id);
  url.popup(width, height, "Bon de commande");
}
</script>

{{assign var=order value=$object}}

<table class="tbl">
  <tr>
    <td colspan="2">
      <button type="button" class="print notext" onclick="popupOrderForm({{$object->_id}})">{{tr}}Print{{/tr}}</button>
      {{mb_value object=$object field=order_number}}
    </td>
    <td colspan="5">{{mb_value object=$object->_ref_societe field=name}}</td>
  </tr>
  {{if $object->object_id}}
  <tr>
    <td colspan="7">
      {{$object->_ref_object->loadRefsFwd()}}
      {{$object->_ref_object}}
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th class="category">Code</th>
    <th class="category" style="width: auto;">{{mb_title class=CProduct field=name}}</th>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <th class="category">Unités</th>
      <th class="category"></th>
    {{else}}
      <th class="category">{{mb_title class=CProductOrderItem field=quantity}}</th>
    {{/if}}
    <th class="category">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
  
  {{foreach from=$order->_ref_order_items item=curr_item name="foreach_products"}}
  <tr>
    <td style="text-align: right;">{{mb_value object=$curr_item->_ref_reference field=supplier_code}}</td>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</td>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <td style="text-align: right; white-space: nowrap;">
        {{$curr_item->_unit_quantity}}
      </td>
      <td style="white-space: nowrap;">{{$curr_item->_ref_reference->_ref_product->item_title}}</td>
    {{else}}
      <td style="text-align: center; white-space: nowrap;">{{mb_value object=$curr_item field=quantity}}</td>
    {{/if}}
    <td style="white-space: nowrap; text-align: right;">
      {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
        {{mb_value object=$curr_item->_ref_reference field=_unit_price}}
      {{else}}
        {{mb_value object=$curr_item field=unit_price}}
      {{/if}}
    </td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=_price}}</td>
  </tr>
  {{/foreach}}
  
  <tr>
    <td colspan="10" style="padding: 0.5em; font-size: 1.1em;">
      <span style="float: right;">
        <strong>{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total}}</strong><br />
        {{mb_label object=$order->_ref_societe field=carriage_paid}} : {{mb_value object=$order->_ref_societe field=carriage_paid}}
      </span>
    </td>
  </tr>
</table>
