<table class="tbl">
  <tr>
    <th>Produit</th>
    <th>Quantité</th>
    <th>Prix</th>
    <th>P.U.</th>
    <th style="width: 1%;"></th>
  </tr>
  
  <!-- Products list -->
  {{foreach from=$list_products item=curr_product}}
  
  <!-- Références list of this Product -->
  {{foreach from=$curr_product->_ref_references item=curr_reference}}
    <tr>
      <td>{{$curr_product->_view}}</td>
      <td>{{mb_value object=$curr_reference field=quantity}}</td>
      <td>{{mb_value object=$curr_reference field=price}}</td>
      <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
      <td>
        <form name="product-reference-{{$curr_reference->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_order_item_aed" />
          <input type="hidden" name="order_item_id" value="0" />
          <input type="hidden" name="order_id" value="{{$order_id}}" />
          <input type="hidden" name="reference_id" value="{{$curr_reference->_id}}" />
          <input type="text" name="quantity" value="1" size="2" />
          <button class="add notext" type="button" onclick="submitOrderItem(this.form, {refreshLists: true})">
            Ajouter
          </button>
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="5">Aucune réference pour ce produit</td>
    </tr>
  {{/foreach}}
{{foreachelse}}
  <tr>
    <td colspan="5">Aucun produit dans cette catégorie</td>
  </tr>
{{/foreach}}
</table>