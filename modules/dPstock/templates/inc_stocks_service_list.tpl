<div id="list-stocks-service-total-count" style="display: none;">{{$list_stocks_service_count}}</div>

<table class="tbl">
  <tr>
    <th>{{tr}}CProductStockService-product_id{{/tr}}</th>
    <th>{{tr}}CProductStockService-service_id{{/tr}}</th>
    <th>{{tr}}CProductStockService-quantity{{/tr}}</th>
    <th></th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks_service item=curr_stock_service}}
  <tr>
    <td><a href="?m={{$m}}&amp;tab=vw_idx_stock_service&amp;stock_service_id={{$curr_stock_service->_id}}" title="{{tr}}CProductStockService.modify{{/tr}}">{{$curr_stock_service->_ref_product->_view}}</a></td>
    <td>{{$curr_stock_service->_ref_service->_view}}</td>
    <td>{{$curr_stock_service->quantity}}</td>
    <td>{{include file="inc_bargraph.tpl" stock=$curr_stock_service}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="3">{{tr}}CProductStockService.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

