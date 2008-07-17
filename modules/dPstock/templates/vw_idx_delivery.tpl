{{mb_include_script module=dPstock script=filter}}
{{mb_include_script module=dPstock script=refresh_value}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["category_id", "keywords"];
  stocksFilter = new Filter("filter-stocks", "{{$m}}", "httpreq_vw_delivery_stocks_list", "delivery-list-stocks", filterFields);
  stocksFilter.submit();
  
  refreshDeliveriesList();
});

function refreshDeliveriesList() {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_deliveries_list");
  url.requestUpdate("deliveries", { waitingText: null } );
}

function refreshStock(stock_id) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_delivery_stock_item");
  url.addParam("stock_id", stock_id);
  url.requestUpdate("delivery-"+stock_id, { waitingText: null } );
}

function deliver(oForm, sign) {
  if (sign == undefined) sign = 1;
  oForm.function_id.value = $V($('function_id'));
  oForm.quantity.value = $V(oForm.quantity) * sign;
  stock_id = $V(oForm.stock_id);
  
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function() {
      refreshValue('stock-'+stock_id+'-bargraph', 'CProductStockGroup', stock_id, 'bargraph');
      refreshDeliveriesList();
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

      <div id="delivery-list-stocks"></div>
      
      <label for="function_id">{{tr}}CProductDelivery-function_id{{/tr}} </label>
      <select name="function_id" id="function_id">
        {{foreach from=$list_functions item=curr_function}}
        <option value="{{$curr_function->_id}}">{{$curr_function->_view}}</option>
        {{/foreach}}
      </select>

    </td>
    <td class="halfPane" id="deliveries"></td>
  </tr>
</table>

