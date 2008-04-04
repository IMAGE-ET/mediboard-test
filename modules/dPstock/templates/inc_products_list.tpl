<table class="tbl">
  <tr>
    <th>Nom</th>
    <th>Fabriquant</th>
    <th>Description</th>
    <th>Code</th>
  </tr>
  {{foreach from=$list_products item=curr_product}}
    <tr>
      <td><a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_product->_id}}" title="Voir ou modifier le produit">{{$curr_product->name}}</a></td>
      <td>{{$curr_product->_ref_societe->_view}}</td>
      <td>{{$curr_product->description|nl2br}}</td>
      <td>{{$curr_product->code}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3">Aucun produit</td>
    </tr>
  {{/foreach}}
</table>