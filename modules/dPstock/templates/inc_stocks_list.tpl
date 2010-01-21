{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="list-stocks-{{if $stock->_class_name == 'CProductStockService'}}service{{else}}group{{/if}}-total-count" style="display: none;">{{$list_stocks_count}}</div>

<table class="tbl">
  <tr>
    <th>{{mb_title object=$stock field=product_id}}</th>
    {{if $stock->_class_name == 'CProductStockService'}}
      <th>{{mb_title object=$stock field=service_id}}</th>
      <th>{{mb_title object=$stock field=common}}</th>
    {{/if}}
    <th>{{mb_title object=$stock field=quantity}}</th>
    <th>{{mb_title object=$stock field=_package_quantity}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks item=_stock}}
  <tr {{if $stock_id == $_stock->_id}}class="selected"{{/if}}>
    <td>
      <a href="?m={{$m}}&amp;tab={{if $stock->_class_name == 'CProductStockService'}}vw_idx_stock_service&amp;stock_service_id{{else}}vw_idx_stock_group&amp;stock_id{{/if}}={{$_stock->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_stock->_ref_product->_guid}}')">
          {{$_stock->_ref_product->_view|truncate:60}}
        </span>
      </a>
    </td>
    {{if $stock->_class_name == 'CProductStockService'}}
      <td>{{$_stock->_ref_service}}</td>
      <td>{{tr}}{{$_stock->common|ternary:'Yes':''}}{{/tr}}</td>
    {{/if}}
    <td style="text-align: right;">
		  <strong>{{$_stock->quantity}}</strong>
		</td>
    <td>
			=
      {{if $_stock->_package_quantity > 0}}
        {{$_stock->_package_quantity}} {{$_stock->_ref_product->packaging}} + 
      {{/if}}
      {{$_stock->_package_mod-0}}
    </td>
    <td>{{include file="inc_bargraph.tpl" stock=$_stock}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}{{$stock->_class_name}}.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

