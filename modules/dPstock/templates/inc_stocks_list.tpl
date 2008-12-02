{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<div id="list-stocks-{{if $stock->_class_name == 'CProductStockService'}}service{{else}}group{{/if}}-total-count" style="display: none;">{{$list_stocks_count}}</div>

<table class="tbl">
  <tr>
    <th>{{tr}}{{$stock->_class_name}}-product_id{{/tr}}</th>
    {{if $stock->_class_name == 'CProductStockService'}}
      <th>{{tr}}CProductStockService-service_id{{/tr}}</th>
    {{/if}}
    <th>{{tr}}{{$stock->_class_name}}-quantity{{/tr}}</th>
    <th>{{tr}}{{$stock->_class_name}}-_package_quantity-court{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks item=curr_stock}}
  <tr>
    <td>
      {{if $stock->_class_name == 'CProductStockService'}}
        <a href="?m={{$m}}&amp;tab=vw_idx_stock_service&amp;stock_service_id={{$curr_stock->_id}}" title="{{tr}}{{$stock->_class_name}}.modify{{/tr}}">{{$curr_stock->_ref_product->_view}}</a>
      {{else}}
        <a href="?m={{$m}}&amp;tab=vw_idx_stock_group&amp;stock_id={{$curr_stock->_id}}" title="{{tr}}{{$stock->_class_name}}.modify{{/tr}}">{{$curr_stock->_ref_product->_view}}</a>
      {{/if}}
    </td>
    {{if $stock->_class_name == 'CProductStockService'}}
      <td>{{$curr_stock->_ref_service->_view}}</td>
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
    <td colspan="5">{{tr}}{{$stock->_class_name}}.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

