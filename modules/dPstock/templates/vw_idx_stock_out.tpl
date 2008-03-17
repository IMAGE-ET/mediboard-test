{{mb_include_script module="dPstock" script="product_selector"}}

<script type="text/javascript">
function pageMain() {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_stock_out_stocks_list");
  url.addParam("category_id", {{$category->category_id}});
  url.requestUpdate("stock-out-list-stocks", { waitingText: null } );
}

function refreshStock(stock_id) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_stock_out_stock_item");
  url.addParam("stock_id", stock_id);
  url.requestUpdate("stock-out-"+stock_id, { waitingText: null } );
}

function stockOut(oForm, stock_id) {
  oForm.stock_id.value = stock_id;
  oForm.quantity.value = oForm['_quantity['+stock_id+']'].value;
  oForm.product_code.value = oForm['_product_code['+stock_id+']'].value;
  oForm.function_id.value = oForm['_function_id['+stock_id+']'].value;
  submitFormAjax(oForm, 'systemMsg', {onComplete: function() {refreshStock(stock_id)} });
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
    {{include file="inc_category_selector.tpl"}}
    {{if $category->category_id}}
      <h3>{{$category->_view}}</h3>
      <div id="stock-out-list-stocks">
      {{include file="inc_aed_stock_out_stocks_list.tpl"}}
      </div>
    {{/if}}
    </td>
  </tr>
</table>
