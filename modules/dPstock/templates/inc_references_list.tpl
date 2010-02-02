{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th style="width: 1%;">{{mb_title class=CProductReference field=code}}</th>
    <th>{{mb_label class=CProductReference field=societe_id}}</th>
    <th style="width: 1%;">{{mb_title class=CProductReference field=price}}</th>
    <th colspan="2" style="width: 1%;">{{mb_title class=CProductReference field=_cond_price}}</th>
    <th colspan="2" style="width: 1%;">{{mb_title class=CProductReference field=_unit_price}}</th>
    {{if $order_form}}
      <th style="width: 1%;"></th>
    {{/if}}
  </tr>
  
  <!-- Références list -->
  {{foreach from=$list_references item=_reference}}
	{{assign var=_product value=$_reference->_ref_product}}
  <tbody class="hoverable">
    <tr {{if $_reference->_id == $reference_id}}class="selected"{{/if}}>
      <td colspan="10">
        {{if !$order_form}}
          <a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$_reference->_id}}" >
        {{/if}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_product->_guid}}')">
          {{$_product->_view|truncate:60}}
        </span>
        {{if !$order_form}}
          </a>
        {{/if}}
      </td>
      
      {{if $order_form}}
      <td rowspan="2">
        {{assign var=id value=$_reference->_id}}
        {{assign var=packaging value=$_product->packaging}}
        <form name="product-reference-{{$id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_item_aed" />
          <input type="hidden" name="reference_id" value="{{$_reference->_id}}" />
          <input type="hidden" name="callback" value="orderItemCallback" />
          <input type="hidden" name="_create_order" value="true" />
          {{mb_field object=$_reference 
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
		
    <tr {{if $_reference->_id == $reference_id}}class="selected"{{/if}}>
      <td style="padding-left: 1em;" {{if $_reference->cancelled}}class="cancelled"{{/if}}>
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_reference->_guid}}')">
          {{if $_reference->code}}
            {{mb_value object=$_reference field=code}}
          {{else}}
            [Aucun code]
          {{/if}}
        </strong>
			</td>
      <td>{{$_reference->_ref_societe}}</td>

		  <td style="text-align: right;">
			  {{mb_value object=$_reference field=price}}
			 </td>
		  <td style="text-align: right;">
		    <label title="{{$_reference->quantity}} {{$_product->packaging}}">
		      {{mb_value object=$_reference field=quantity}} 
		    </label>
		    x
		 </td>
		  <td style="text-align: right;">
			  {{mb_value object=$_reference field=_cond_price}}
			</td>
		  <td style="text-align: right;">
		    <label title="{{$_reference->quantity}} {{$_product->packaging}} x {{$_product->quantity}} {{$_product->item_title}}">
		      {{mb_value object=$_reference field=_unit_quantity}} x
		    </label>
		   </td>
		  <td style="text-align: right;"><strong>{{mb_value object=$_reference field=_unit_price}}</strong></td>
    </tr>
  
	</tbody>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>