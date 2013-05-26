{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$total current=$start step=$conf.dPstock.CProductReference.pagination_size}}

<table class="tbl">
  <tr>
    <th style="width: 16px;"></th>
    <th class="narrow">{{mb_title class=CProductReference field=code}}</th>
    <th>{{mb_label class=CProductReference field=societe_id}}</th>
    
    {{if $conf.dPstock.CProductReference.show_cond_price}}
      <th class="narrow">{{mb_title class=CProductReference field=_cond_price}}</th>
    {{/if}}
    
    <th class="narrow">{{mb_title class=CProductReference field=price}}</th>
    
    {{if $mode}}
      {{if $mode == "order"}}<th class="narrow">Stock</th>{{/if}}
      <th class="narrow"></th>
    {{/if}}
  </tr>
  
  <!-- Références list -->
  {{foreach from=$list_references item=_reference}}
	{{assign var=_product value=$_reference->_ref_product}}
  <tbody class="hoverable">
    <tr class="{{if $_reference->_id == $reference_id}}selected{{/if}}">
      <td rowspan="2" {{if $_product->_in_order}}class="ok"{{/if}}>
        {{mb_include module=stock template=inc_product_in_order product=$_product}}
      </td>
      <td colspan="{{if $conf.dPstock.CProductReference.show_cond_price}}{{if $mode == 'order'}}5{{else}}4{{/if}}{{else}}{{if $mode == 'order'}}5{{else}}4{{/if}}{{/if}}">
        <a href="{{if !$mode}}?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id={{$_reference->_id}}{{else}}#1{{/if}}">
          <strong onmouseover="ObjectTooltip.createEx(this, '{{$_product->_guid}}')" {{if $mode}}onclick="showProductDetails({{$_product->_id}})"{{/if}}>
            {{$_product->_view|truncate:60}}
          </strong>
        </a>
      </td>
      
      {{if $mode}}
      <td rowspan="2">
        {{assign var=id value=$_reference->_id}}
        {{if $mode == "order"}}
          <form name="product-reference-{{$id}}" action="?" method="post">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="reference_id" value="{{$_reference->_id}}" />
            <input type="hidden" name="callback" value="orderItemCallback" />
            <input type="hidden" name="_create_order" value="1" />
            <input type="hidden" name="reception_id" value="" />
            {{mb_field object=$_reference 
              field=quantity 
              size=2
              form="product-reference-$id" 
              increment=true 
              style="width: 2em;"
            }}
            <button class="add notext" type="button" onclick="submitOrderItem(this.form, {refreshLists: false})" title="{{tr}}Add{{/tr}}">{{tr}}Add{{/tr}}</button>
          </form>
        {{else if $mode == "reception"}}
          <form name="product-reference-{{$id}}" action="?" method="post">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
            <input type="hidden" name="_reference_id" value="{{$_reference->_id}}" />
            <input type="hidden" name="date" value="now" />
            <input type="hidden" name="callback" value="receptionCallback" />
            <input type="hidden" name="reception_id" value="" />
            {{mb_field object=$_reference 
              field=quantity 
              size=2
              form="product-reference-$id" 
              increment=true 
              value=1
              style="width: 2em;"
            }}
            <input type="text" name="code" value="" size="6" title="{{tr}}CProductOrderItemReception-code{{/tr}}" />
            <input type="text" name="lapsing_date" value="" class="date mask|99/99/9999 format|$3-$2-$1" title="{{tr}}CProductOrderItemReception-lapsing_date{{/tr}}" />
            <button class="tick notext" type="button" onclick="this.form.reception_id.value = window.reception_id; submitOrderItem(this.form, {refreshLists: false})" title="{{tr}}Add{{/tr}}">{{tr}}Add{{/tr}}</button>
          </form>
        {{/if}}
      </td>
      {{/if}}
    </tr>
		
    <tr {{if $_reference->_id == $reference_id}}class="selected"{{/if}}>
      <td style="padding-left: 1em;" {{if $_reference->cancelled}}class="cancelled"{{/if}}>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_reference->_guid}}')">
          {{if $_reference->code}}
            {{mb_value object=$_reference field=code}}
          {{else}}
            [Aucun code]
          {{/if}}
        </span>
			</td>
      <td>{{$_reference->_ref_societe}}</td>
			 
      {{if $conf.dPstock.CProductReference.show_cond_price}}
	 	  <td style="text-align: right;">
        <label title="{{$_reference->quantity}} {{$_product->item_title}}">
			    {{mb_value object=$_reference field=_cond_price}}
        </label>
			</td>
	    {{/if}}
			
		  <td style="text-align: right; font-weight: bold;">
        <label title="{{$_reference->quantity}} x {{$_product->item_title}}">
          {{mb_value object=$_reference field=price}}
        </label>
      </td>
      
      {{if $mode == "order"}}
      <td style="text-align: right;">
        {{if $_product->_ref_stock_group}}
        <table class="main layout">
          <tr>
            <td>{{mb_include module=stock template=inc_bargraph stock=$_product->_ref_stock_group}}</td>
            <td style="width: 3em;">{{$_product->_ref_stock_group->quantity}}</td>
          </tr>
        </table>
        {{/if}}
      </td>
      {{/if}}
    </tr>
  
	</tbody>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>