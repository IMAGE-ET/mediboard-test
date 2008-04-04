<table class="tbl">
  <tr>
    <th>Produit</th>
    <th>Fournisseur</th>
    <th>Quantité</th>
    <th>Prix</th>
    <th>P.U.</th>
    {{if $order_id}}<th style="width: 1%;"></th>{{/if}}
  </tr>
  
  <!-- Références list -->
  {{foreach from=$list_references item=curr_reference}}
  <tr>
    <td>
    {{if !$order_id}}
      <a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}" title="Voir ou modifier la référence">{{$curr_reference->_ref_product->_view}}</a>
    {{else}}
      {{$curr_reference->_ref_product->_view}}
    {{/if}}
    </td>
    <td>{{$curr_reference->_ref_societe->_view}}</td>
    <td>{{mb_value object=$curr_reference field=quantity}}</td>
    <td class="currency">{{mb_value object=$curr_reference field=price}}</td>
    <td class="currency">{{mb_value object=$curr_reference field=_unit_price}}</td>
    {{if $order_id}}
    <td>
      <form name="product-reference-{{$curr_reference->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_order_item_aed" />
        <input type="hidden" name="order_item_id" value="0" />
        <input type="hidden" name="order_id" value="{{$order_id}}" />
        <input type="hidden" name="reference_id" value="{{$curr_reference->_id}}" />
        <input type="text" name="quantity" value="1" size="2" />
        <button class="add notext" type="button" onclick="submitOrderItem(this.form, {refreshLists: true})">Ajouter</button>
      </form>
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="6">Aucune référence trouvée</td>
  </tr>
{{/foreach}}
</table>