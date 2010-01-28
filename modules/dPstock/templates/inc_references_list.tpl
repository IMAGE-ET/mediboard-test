{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$total current=$start step=15}}

<table class="tbl">
  <tr>
    <th rowspan="2">{{mb_title class=CProductReference field=societe_id}}</th>
    <th rowspan="2">{{mb_title class=CProductReference field=code}}</th>
    <th rowspan="2">{{mb_title class=CProductReference field=quantity}}</th>
    <th colspan="2">{{mb_title class=CProductReference field=price}}</th>
    {{if $order_form}}
      <th style="width: 1%;" rowspan="2"></th>
    {{/if}}
  </tr>
  <tr>
    <th style="width: 1%;">{{mb_title class=CProductReference field=_unit_price}}</th>
    <th>{{mb_title class=CProductReference field=_sub_unit_price}}</th>
  </tr>
  
  <!-- Références list -->
  {{foreach from=$list_references item=curr_reference}}
  <tbody class="hoverable">
    <tr>
      <td colspan="4">
        {{if !$order_form}}
          <a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}" >
        {{/if}}
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$curr_reference->_ref_product->_guid}}')">
          {{$curr_reference->_ref_product->_view|truncate:80}}
        </strong>
        {{if !$order_form}}
          </a>
        {{/if}}
      </td>
      
      <td style="text-align: right; padding-right: 1em;">
        {{mb_value object=$curr_reference field=price decimals=4}}
      </td>
      
      {{if $order_form}}
      <td rowspan="2">
        {{assign var=id value=$curr_reference->_id}}
        {{assign var=packaging value=$curr_reference->_ref_product->packaging}}
        <form name="product-reference-{{$id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_item_aed" />
          <input type="hidden" name="reference_id" value="{{$curr_reference->_id}}" />
          <input type="hidden" name="callback" value="orderItemCallback" />
          <input type="hidden" name="_create_order" value="true" />
          {{mb_field object=$curr_reference 
            field=quantity 
            size=2
            form="product-reference-$id" 
            increment=true 
            value=1
            style="width: 2em;"
          }}
          <button class="add notext" type="button" onclick="submitOrderItem(this.form, {refreshLists: false})" title="{{tr}}Add{{/tr}}">{{tr}}Add{{/tr}}</button>
        </form>
      </td>
      {{/if}}
    </tr>
    <tr>
      <td style="padding-left: 2em;">{{$curr_reference->_ref_societe}}</td>
      <td>{{mb_value object=$curr_reference field=code}}</td>
      <td>{{mb_value object=$curr_reference field=quantity}} {{mb_value object=$curr_reference->_ref_product field=packaging}}</td>
      <td style="text-align: right;">{{mb_value object=$curr_reference field=_unit_price decimals=4}}</td>
      <td style="text-align: right; font-weight: bold;">{{mb_value object=$curr_reference field=_sub_unit_price decimals=4}}</td>
    </tr>
  </tbody>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>