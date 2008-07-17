<table class="tbl">
  <tr>
    <th>{{tr}}CProductStockGroup-product_id{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
    <th></th>
  </tr>
{{foreach from=$list_stocks item=curr_stock}}
  <tbody id="stock-out-{{$curr_stock->_id}}">
  {{include file="inc_aed_stock_out_stock_item.tpl" stock=$curr_stock}}
  </tbody>
{{foreachelse}}
  <tr>
    <td colspan="8">{{tr}}CProductStockGroup.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>