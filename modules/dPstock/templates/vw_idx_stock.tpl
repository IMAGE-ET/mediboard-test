{{mb_include_script module=dPstock script=product_selector}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
function pageMain() {
  filterFields = ["category_id", "keywords", "only_ordered_stocks"];
  stocksFilter = new Filter("filter-stocks", "{{$m}}", "httpreq_vw_stocks_list", "list-stocks", filterFields);
  stocksFilter.submit();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">

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
        <button type="button" class="cancel notext" onclick="stocksFilter.empty();"></button><br />
        
        <input type="checkbox" name="only_ordered_stocks" onchange="stocksFilter.submit();" />
        <label for="only_ordered_stocks">Seulement les stocks en cours de réapprovisionnement</label>
      </form>
      
      <div id="list-stocks"></div>
    </td>
    
    <!-- Edit/New Stock form -->
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0">
        {{tr}}CProductStock.create{{/tr}}
      </a>
      <form name="edit_stock" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="group_id" value="{{$g}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if $stock->_id}}
        <th class="title modify" colspan="2">{{tr}}CProductStock.modify{{/tr}} {{$stock->_view}}</th>
        {{else}}
        <th class="title" colspan="2">{{tr}}CProductStock.create{{/tr}}</th>
        {{/if}}
        <tr>
          <th>{{mb_label object=$stock field="quantity"}}</th>
          <td>{{mb_field object=$stock field="quantity" form="edit_stock" increment="1" min=0}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="product_id"}}</th>
          <td class="readonly">
            <input type="hidden" name="product_id" value="{{$stock->product_id}}" class="{{$stock->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$stock->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search" type="button" onclick="ProductSelector.init()">{{tr}}Search{{/tr}}</button>
            <script type="text/javascript">
            ProductSelector.init = function(){
              this.sForm = "edit_stock";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$stock->product_id}});
            }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_critical"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_critical" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_min"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_min" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_optimum"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_optimum" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_max"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_max" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $stock->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$stock->_view|smarty:nodefaults|JSAttribute}}'})">
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