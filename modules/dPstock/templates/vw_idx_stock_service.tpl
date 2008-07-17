{{mb_include_script module=dPstock script=product_selector}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["category_id", "service_id", "keywords", "limit"];
  stocksServiceFilter = new Filter("filter-stocks-service", "{{$m}}", "httpreq_vw_stocks_service_list", "list-stocks-service", filterFields);
  stocksServiceFilter.submit();
});
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="filter-stocks-service" action="?" method="post" onsubmit="return stocksServiceFilter.submit('keywords');">
        <input type="hidden" name="m" value="{{$m}}" />
        <select name="category_id" onchange="stocksServiceFilter.submit();">
          <option value="0">&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
          {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
          {{/foreach}}
        </select>
        <select name="service_id" onchange="stocksServiceFilter.submit();">
          <option value="0">&mdash; {{tr}}CService.all{{/tr}} &mdash;</option>
          {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->_view}}</option>
          {{/foreach}}
        </select>
        <input type="text" name="keywords" value="" />
        <input type="hidden" name="limit" value="" />
        <button type="button" class="search" onclick="stocksServiceFilter.submit('keywords');">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="stocksServiceFilter.empty();"></button>
      </form>
  
      <div id="list-stocks-service"></div>
    </td>

    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_stock_service&amp;stock_id=0">{{tr}}CProductStockService.create{{/tr}}</a>
      <form name="edit_stock_service" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_service_aed" />
      <input type="hidden" name="stock_id" value="{{$stock_service->_id}}" />
      <input type="hidden" name="service_id" value="{{$stock_service->service_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $stock_service->_id}}
          <th class="title modify" colspan="2">{{tr}}CProductStockService.modify{{/tr}} {{$stock_service->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CProductStockService.create{{/tr}}</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$stock_service field="quantity"}}</th>
          <td>{{mb_field object=$stock_service field="quantity" form="edit_stock_service" increment="1" min=0}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock_service field="product_id"}}</th>
          <td class="readonly">
          <input type="hidden" name="product_id" value="{{$stock_service->product_id}}" class="{{$stock_service->_props.product_id}}" />
          <input type="text" name="product_name" value="{{$stock_service->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
          <button class="search" type="button" onclick="ProductSelector.init()">{{tr}}Search{{/tr}}</button>
          <script type="text/javascript">
            ProductSelector.init = function(){
              this.sForm = "edit_stock_service";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$stock_service->product_id}});
            }
          </script>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$stock_service field="service_id"}}</th>
        <td>
          <select name="service_id">
            <option value="0">&mdash; {{tr}}CService.select{{/tr}} &mdash;</option>
            {{foreach from=$list_services item=curr_service}}
            <option value="{{$curr_service->_id}}" {{if $stock_service->service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <td class="button" colspan="4">{{if $stock_service->_id}}
          <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$stock_service->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
          {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
    </form>
    </td>
  </tr>
</table>
