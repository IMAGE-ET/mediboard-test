{{mb_include_script module=dPstock script=filter}}
{{mb_include_script module=dPstock script=refresh_value}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["category_id", "keywords"];
  stocksFilter = new Filter("filter-stocks", "{{$m}}", "httpreq_vw_stock_out_stocks_list", "stock-out-list-stocks", filterFields);
  stocksFilter.submit();
  
  refreshStockOutsList();
});

function refreshStockOutsList() {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_stock_outs_list");
  url.requestUpdate("stock-outs", { waitingText: null } );
}

function refreshStock(stock_id) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_stock_out_stock_item");
  url.addParam("stock_id", stock_id);
  url.requestUpdate("stock-out-"+stock_id, { waitingText: null } );
}

function stockOut(oForm, sign) {
  if (sign == undefined) sign = 1;
  oForm.function_id.value = $V($('function_id'));
  oForm.quantity.value = $V(oForm.quantity) * sign;
  stock_id = $V(oForm.stock_id);
  
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function() {
      refreshValue('stock-'+stock_id+'-bargraph', 'CProductStockGroup', stock_id, 'bargraph');
      refreshStockOutsList();
    }
  });
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">

      <form name="filter-stocks" action="?" method="post" onsubmit="return stocksFilter.submit('keywords');">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <select name="category_id" onchange="stocksFilter.submit();">
          <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
        {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" />
        <button type="button" class="search" onclick="stocksFilter.submit('keywords');">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="stocksFilter.empty();">{{tr}}Reset{{/tr}}</button><br />
      </form>

      <div id="stock-out-list-stocks"></div>
      
      <label for="function_id">{{tr}}CProductStockOut-function_id{{/tr}} </label>
      <select name="function_id" id="function_id">
        {{foreach from=$list_functions item=curr_function}}
        <option value="{{$curr_function->_id}}">{{$curr_function->_view}}</option>
        {{/foreach}}
      </select>

    </td>
    <td class="halfPane" id="stock-outs"></td>
  </tr>
</table>

