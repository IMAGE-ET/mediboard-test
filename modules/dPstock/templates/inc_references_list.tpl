{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="list-references-total-count" style="display: none;">{{$list_references_count}}</div>

<table class="tbl">
  <tr>
    <th>{{tr}}CProductReference-product_id{{/tr}}</th>
    {{if !$hide_societes}}
      <th>{{tr}}CProductReference-societe_id{{/tr}}</th>
    {{/if}}
    <th>{{tr}}CProductReference-code{{/tr}}</th>
    <th>{{tr}}CProductReference-quantity{{/tr}}</th>
    <th>{{tr}}CProduct-_quantity{{/tr}}</th>
    <th>{{tr}}CProductReference-price{{/tr}}</th>
    <th>{{tr}}CProductReference-_unit_price{{/tr}}</th>
    {{if $order_id}}<th style="width: 1%;"></th>{{/if}}
  </tr>
  
  <!-- Références list -->
  {{foreach from=$list_references item=curr_reference}}
  <tr>
    <td>
    {{if !$order_id}}
      <a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}" title="{{tr}}CProductReference.modify{{/tr}}">{{$curr_reference->_ref_product->_view}}</a>
    {{else}}
      {{$curr_reference->_ref_product->_view}}
    {{/if}}
    </td>
    {{if !$hide_societes}}
      <td>{{$curr_reference->_ref_societe->_view}}</td>
    {{/if}}
    <td>{{mb_value object=$curr_reference field=code}}</td>
    <td>{{mb_value object=$curr_reference field=quantity}} {{mb_value object=$curr_reference->_ref_product field=packaging}}</td>
    <td>{{$curr_reference->_ref_product->_quantity}}</td>
    <td class="currency">{{mb_value object=$curr_reference field=price decimals=5}}</td>
    <td class="currency">{{mb_value object=$curr_reference field=_unit_price decimals=5}}</td>
    {{if $order_id}}
    <td>
      {{assign var=id value=$curr_reference->_id}}
      {{assign var=packaging value=$curr_reference->_ref_product->packaging}}
      <form name="product-reference-{{$id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_order_item_aed" />
        <input type="hidden" name="order_id" value="{{$order_id}}" />
        <input type="hidden" name="reference_id" value="{{$curr_reference->_id}}" />
        {{mb_field object=$curr_reference field=quantity size=2 form="product-reference-$id" increment=true value=1 title="$packaging"}}
        <button class="add notext" type="button" onclick="submitOrderItem(this.form, {refreshLists: false})" title="{{$packaging}}">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>