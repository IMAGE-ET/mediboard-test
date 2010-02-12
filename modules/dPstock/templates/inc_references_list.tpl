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
    <th style="width: 1%;">{{mb_title class=CProductReference field=code}}</th>
    <th>{{mb_label class=CProductReference field=societe_id}}</th>
    <th style="width: 1%;">{{mb_title class=CProductReference field=price}}</th>
    {{if $dPconfig.dPstock.CProductReference.show_cond_price}}
    <th style="width: 1%;">{{mb_title class=CProductReference field=_cond_price}}</th>
    {{/if}}
    <th style="width: 1%;">{{mb_title class=CProductReference field=_unit_price}}</th>
    {{if $mode}}
      <th style="width: 1%;"></th>
    {{/if}}
  </tr>
  
  <!-- Références list -->
  {{foreach from=$list_references item=_reference}}
	{{assign var=_product value=$_reference->_ref_product}}
  <tbody class="hoverable">
    <tr {{if $_reference->_id == $reference_id}}class="selected"{{/if}}>
      <td colspan="{{$dPconfig.dPstock.CProductReference.show_cond_price|ternary:5:4}}">
        {{if !$mode}}
          <a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$_reference->_id}}" >
        {{/if}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_product->_guid}}')">
          {{$_product->_view|truncate:60}}
        </span>
        {{if !$mode}}
          </a>
        {{/if}}
      </td>
      
      {{if $mode}}
      <td rowspan="2">
        {{assign var=id value=$_reference->_id}}
        {{if $mode == "order"}}
          <form name="product-reference-{{$id}}" action="?" method="post">
            <input type="hidden" name="m" value="{{$m}}" />
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
              value=1
              style="width: 2em;"
            }}
            <button class="add notext" type="button" onclick="submitOrderItem(this.form, {refreshLists: false})" title="{{tr}}Add{{/tr}}">{{tr}}Add{{/tr}}</button>
          </form>
        {{else if $mode == "reception"}}
          <form name="product-reference-{{$id}}" action="?" method="post">
            <input type="hidden" name="m" value="{{$m}}" />
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
			 
      {{if $dPconfig.dPstock.CProductReference.show_cond_price}}
	 	  <td style="text-align: right;">
        <label title="{{$_reference->quantity}} {{$_product->packaging}}">
			    {{mb_value object=$_reference field=_cond_price}}
        </label>
			</td>
	    {{/if}}
			
		  <td style="text-align: right; font-weight: bold;">
        <label title="{{$_reference->quantity}} {{$_product->packaging}} x {{$_product->quantity}} {{$_product->item_title}}">
          {{mb_value object=$_reference field=_unit_price}}
        </label>
      </td>
    </tr>
  
	</tbody>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>