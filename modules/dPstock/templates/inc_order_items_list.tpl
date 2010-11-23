{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="{{if !@$screen}}grid print{{else}}main tbl{{/if}}">
  <thead>
    <tr>
      <th class="category">Code</th>
      {{if $order->object_id || $order->_has_lot_numbers}}
        <th class="category">Lot</th>
        <th class="category">Date pér.</th>
      {{/if}}
      <th class="category" style="width: auto;">{{mb_title class=CProduct field=name}}</th>
      {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
        <th class="category">Unités</th>
        <th class="category"></th>
      {{else}}
        <th class="category">{{mb_title class=CProductOrderItem field=quantity}}</th>
      {{/if}}
      {{if $order->object_id || $order->comments|strpos:"Bon de retour" === 0}}
        <th class="category">{{mb_title class=CProductOrderItem field=renewal}}</th>
      {{/if}}
      <th class="category">{{mb_title class=CProductOrderItem field=unit_price}}</th>
      <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
      <th class="category">{{mb_title class=CProductOrderItem field=tva}}</th>
    </tr>
  </thead>
  
  {{foreach from=$order->_ref_order_items item=curr_item}}
  <tr>
    <td style="text-align: right; white-space: nowrap;">
      {{if $curr_item->_ref_reference->supplier_code}}
        {{mb_value object=$curr_item->_ref_reference field=supplier_code}}
      {{else}}
        {{mb_value object=$curr_item->_ref_reference->_ref_product field=code}}
      {{/if}}
    </td>
    
    {{if $order->object_id || $order->_has_lot_numbers}}
      {{if $curr_item->_ref_lot}}
        <td>{{mb_value object=$curr_item->_ref_lot field=code}}</td>
        <td>{{mb_value object=$curr_item->_ref_lot field=lapsing_date}}</td>
      {{else}}
        <td></td>
        <td></td>
      {{/if}}
    {{/if}}
    
    <td>
      <strong>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</strong>
      
      {{if $curr_item->septic}}
        (Déstérilisé)
      {{/if}}
    </td>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <td style="text-align: right; white-space: nowrap;">
        {{$curr_item->_unit_quantity}}
      </td>
      <td style="white-space: nowrap;">{{$curr_item->_ref_reference->_ref_product->item_title}}</td>
    {{else}}
      <td style="text-align: center; white-space: nowrap;">{{mb_value object=$curr_item field=quantity}}</td>
    {{/if}}
    
    {{if $order->object_id || $order->comments|strpos:"Bon de retour" === 0}}
      <td>{{mb_value object=$curr_item field=renewal}}</td>
    {{/if}}
    
    <td style="white-space: nowrap; text-align: right;">
      {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
        {{mb_value object=$curr_item->_ref_reference field=_unit_price}}
      {{else}}
        {{mb_value object=$curr_item field=unit_price}}
      {{/if}}
    </td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=_price}}</td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=tva decimals=1}}</td>
  </tr>
  {{/foreach}}
  
  <tr>
    <td colspan="10" style="padding: 0.5em; font-size: 1.1em;">
      <span style="float: right; text-align: right;">
        <strong>{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total}}</strong><br />
        <strong>{{tr}}Total TTC{{/tr}} : {{mb_value object=$order field=_total_tva}}</strong><br />
        {{mb_label object=$order->_ref_societe field=carriage_paid}} : {{mb_value object=$order->_ref_societe field=carriage_paid}}
      </span>
    </td>
  </tr>
</table>