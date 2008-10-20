{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<div id="list-stocks-total-count" style="display: none;">{{$list_stocks_count}}</div>

<table class="tbl">
  <tr>
    <th>{{tr}}CProductStockService-product_id{{/tr}}</th>
    <th>{{tr}}CProductStockService-service_id{{/tr}}</th>
    <th>{{tr}}CProductStockService-quantity{{/tr}}</th>
    <th>{{tr}}CProductStockService-_package_quantity-court{{/tr}}</th>
    <th></th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks item=curr_stock}}
  <tr>
    <td><a href="?m={{$m}}&amp;tab=vw_idx_stock_service&amp;stock_service_id={{$curr_stock->_id}}" title="{{tr}}CProductStockService.modify{{/tr}}">{{$curr_stock->_ref_product->_view}}</a></td>
    <td>{{$curr_stock->_ref_service->_view}}</td>
    <td>{{$curr_stock->quantity}}</td>
    <td>
      {{if $curr_stock->_package_quantity > 1}}
        <b>{{$curr_stock->_package_quantity}}</b>
      {{/if}}
      {{if $curr_stock->_package_quantity > 1 && $curr_stock->_package_mod > 0}} / {{/if}}
      {{if $curr_stock->_package_mod > 0}}
        {{$curr_stock->_package_mod}}
      {{/if}}
    </td>
    <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="3">{{tr}}CProductStockService.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

