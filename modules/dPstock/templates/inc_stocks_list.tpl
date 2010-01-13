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
    <th>{{tr}}{{$stock->_class_name}}-product_id{{/tr}}</th>
    {{if $stock->_class_name == 'CProductStockService'}}
      <th>{{tr}}CProductStockService-service_id{{/tr}}</th>
      <th>{{tr}}CProductStockService-common{{/tr}}</th>
    {{/if}}
    <th>{{tr}}{{$stock->_class_name}}-quantity{{/tr}}</th>
    <th>{{tr}}{{$stock->_class_name}}-_package_quantity-court{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks item=curr_stock}}
  <tr>
    <td>
      <a href="?m={{$m}}&amp;tab={{if $stock->_class_name == 'CProductStockService'}}vw_idx_stock_service&amp;stock_service_id{{else}}vw_idx_stock_group&amp;stock_id{{/if}}={{$curr_stock->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_stock->_ref_product->_guid}}')">
          {{$curr_stock->_ref_product->_view|truncate:60}}
        </span>
      </a>
    </td>
    {{if $stock->_class_name == 'CProductStockService'}}
      <td>{{$curr_stock->_ref_service->_view}}</td>
      <td>{{tr}}{{$curr_stock->common|ternary:'Yes':''}}{{/tr}}</td>
    {{/if}}
    <td>{{$curr_stock->quantity}}</td>
    <td>
      {{*if $curr_stock->_package_quantity > 0 && $curr_stock->_package_mod > 0}}
        {{assign var=both value=true}}
      {{else}}
        {{assign var=both value=false}}
      {{/if*}}
      {{if $curr_stock->_package_quantity > 0}}
        <strong>{{$curr_stock->_package_quantity}} {{$curr_stock->_ref_product->packaging}}</strong> + 
      {{/if}}
      {{$curr_stock->_package_mod-0}}
    </td>
    <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}{{$stock->_class_name}}.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

