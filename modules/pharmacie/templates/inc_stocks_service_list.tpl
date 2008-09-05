<button type="button" class="tick">Dispensation automatique</button>

<table class="tbl">
  <tr>
    <th>{{tr}}CProductStockService-product_id{{/tr}}</th>
    <th>{{tr}}CProductStockService-quantity{{/tr}}</th>
    <th></th>
  </tr>
  {{foreach from=$list_stocks_service item=stock}}
    <tr>
      <td>{{$stock->_ref_product->_view}}</td>
      <td>{{include file="../../dPstock/templates/inc_bargraph.tpl" stock=$stock}}</td>
      <td><button type="button" class="tick">Commander</button></td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="5">{{tr}}CProductStockService.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>