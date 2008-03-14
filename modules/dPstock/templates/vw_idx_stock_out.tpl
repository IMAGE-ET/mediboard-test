{{mb_include_script module="dPstock" script="product_selector"}}

<script type="text/javascript">
function refreshStock(stock_id) {
  /*url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_stock_out_item");
  url.addParam("stock_id", stock_id);
  url.requestUpdate("stock-"+stock_id, { waitingText: null } );*/
}

function stockOut(oForm, stock_id) {
  oForm.stock_id.value = stock_id;
  submitFormAjax(oForm, 'systemMsg',{onComplete: function() {refreshStock(stock_id)} });
}
</script>


<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
    {{include file="inc_vw_category_selector.tpl"}}
    {{if $category->category_id}}
      <h3>{{$category->_view}}</h3>
      <form name="form-stock-out" action="?" methode="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="dosql" value="do_stock_out_aed" />
        <input type="hidden" name="stock_out_id" value="0" />
        <input type="hidden" name="stock_id" value="" />
        <input type="hidden" name="date" value="now" />
        <input type="hidden" name="del" value="0" />
        <table class="tbl">
          <tr>
            <th>Produit</th>
            <th>En stock</th>
            <th>Seuils</th>
            <th>Quantité</th>
            <th>Code produit</th>
            <th>Pour le service</th>
            <th>Déstocker</th>
          </tr>
          
        <!-- Products list -->
        {{foreach from=$category->_ref_products item=curr_product}}
          {{if $curr_product->_ref_stock_group}}
            {{assign var=curr_stock value=$curr_product->_ref_stock_group}}
            <tr id="stock-{{$curr_stock->_id}}">
              <td>{{$curr_product->_view}}</td>
              <td>{{$curr_stock->quantity}}</td>
              <td>{{include file="inc_vw_bargraph.tpl" stock=$curr_stock}}</td>
              <td>{{mb_field object=$stock_out field=quantity}}</td>
              <td>{{mb_field object=$stock_out field=product_code}}</td>
              <td>{{mb_field object=$stock_out field=function_id}}</td>
              <td><button class="tick notext" type="button" onclick="stockOut(this.form, {{$curr_stock->_id}}, )">OK</button></td>
            </tr>
          {{/if}}
        {{foreachelse}}
          <tr>
            <td colspan="3">Aucun produit dans cette catégorie</td>
          </tr>
        {{/foreach}}
        </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>
