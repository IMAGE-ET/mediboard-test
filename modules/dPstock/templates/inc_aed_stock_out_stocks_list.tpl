<form name="form-stock-out" action="?" method="post">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="dosql" value="do_stock_out_aed" />
  <input type="hidden" name="stock_out_id" value="0" />
  <input type="hidden" name="stock_id" value="" />
  <input type="hidden" name="quantity" value="" />
  <input type="hidden" name="product_code" value="" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="_do_stock_out" value="1" />
  <input type="hidden" name="del" value="0" />
  <label for="function_id">Pour le service </label>
  <select name="function_id">
    {{foreach from=$list_functions item=curr_function}}
    <option value="{{$curr_function->_id}}">{{$curr_function->_view}}</option>
    {{/foreach}}
  </select>
  <table class="tbl">
    <tr>
      <th>Produit</th>
      <th>En stock</th>
      <th>Seuils</th>
      <th>Quantité</th>
      <th>Code produit</th>
    </tr>
    
  <!-- Products list -->
  {{foreach from=$category->_ref_products item=curr_product}}
    {{if $curr_product->_ref_stock_group}}
      {{assign var=curr_stock value=$curr_product->_ref_stock_group}}
      <tbody id="stock-out-{{$curr_stock->_id}}">
      {{include file="inc_aed_stock_out_stock_item.tpl" stock=$curr_stock}}
      </tbody>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="3">Aucun produit dans cette catégorie</td>
    </tr>
  {{/foreach}}
  </table>
</form>