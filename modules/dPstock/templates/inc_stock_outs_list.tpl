<table class="tbl">
  <tr>
    <th colspan="5" class="title">20 {{tr}}last{{/tr}} {{tr}}CProductStockOut.more{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductStockOut-date{{/tr}}</th>
    <th>{{tr}}CProductStockOut-quantity{{/tr}}</th>
    <th>{{tr}}CProductStockOut-code{{/tr}}</th>
    <th>{{tr}}CProductStockOut-function_id{{/tr}}</th>
  </tr>
  {{foreach from=$list_latest_stock_outs item=curr_stock_out}}
  <tr>
    <td>{{$curr_stock_out->_ref_stock->_view}}</td>
    <td class="date">{{mb_value object=$curr_stock_out field=date}}</td>
    <td>{{mb_value object=$curr_stock_out field=quantity}}</td>
    <td>{{mb_value object=$curr_stock_out field=code}}</td>
    <td>{{mb_value object=$curr_stock_out->_ref_function field=_view}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductStockOut.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
