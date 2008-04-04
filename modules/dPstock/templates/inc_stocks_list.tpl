<table class="tbl">
  <tr>
    <th>Produit</th>
    <th>En stock</th>
    <th>Seuils</th>
  </tr>
  
<!-- Stocks list -->
{{foreach from=$list_stocks item=curr_stock}}
  <tr>
    <td><a href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->_id}}" title="Voir ou modifier le stock">{{$curr_stock->_ref_product->_view}}</a></td>
    <td>{{$curr_stock->quantity}}</td>
    <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="3">Aucun stock trouvé</td>
  </tr>
{{/foreach}}
</table>
