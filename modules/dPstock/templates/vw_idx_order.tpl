<script type="text/javascript">
function pageMain() {
  regFieldCalendar("edit_order", "date");
  PairEffect.initGroup("productToggle", { bStartVisible: true });
  if({{$order->_id}}){
    reloadOrder({{$order->_id}});
  }
}

function reloadOrder(order_id){
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate("orders_list", { waitingText: null } );
}

function actionOrderItem(order_id, action, object_id){
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.addParam("action", action);
  url.addParam("object_id", object_id);
  url.requestUpdate("orders_list", { waitingText: null } );
}
</script>
<table class="main">
  <tr>
    <td class="halfPane">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="tab" value="vw_idx_order" />
        <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
        <select name="category_id" onchange="this.form.submit()">
          <option value="-1" >&mdash; Choisir une catégorie &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $curr_category->category_id == $category->category_id}}selected="selected"{{/if}}>{{$curr_category->_view}}</option>
        {{/foreach}}
        </select>
        
        <label for="societe_id" title="Choisissez un fournisseur">Fournisseur</label>
        <select name="societe_id" onchange="this.form.submit()">
          <option value="0" >&mdash; Tous les founisseurs &mdash;</option>
        {{foreach from=$list_societes item=curr_societe}} 
          <option value="{{$curr_societe->societe_id}}" {{if $curr_societe->societe_id == $societe->societe_id}}selected="selected"{{/if}}>{{$curr_societe->_view}}</option>
        {{/foreach}}
        </select>
      </form>
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_order&amp;order_id=0">
        Nouvelle commande
      </a>

    {{if $category->category_id}}
    <h3>{{$category->_view}}</h3>
      <table class="tbl">
        <tr>
          <th>Fournisseur</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>P.U.</th>
          <th>Actions</th>
        </tr>
        
        <!-- Products list -->
        {{foreach from=$category->_ref_products item=curr_product}}
        <tr id="product-{{$curr_product->_id}}-trigger">
          <td colspan="5">
            {{$curr_product->_view}} ({{$curr_product->_ref_references|@count}} références)
          </td>
        </tr>
        <tbody class="productToggle" id="product-{{$curr_product->_id}}">
        
        <!-- Références list of this Product -->
        {{foreach from=$curr_product->_ref_references item=curr_reference}}
          <tr {{if $curr_reference->_id == $order->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab=vw_idx_order&amp;reference_id={{$curr_reference->_id}}" title="Voir ou modifier la référence">{{$curr_reference->_ref_societe->_view}}</a></td>
            <td>{{$curr_reference->quantity}}</td>
            <td>{{$curr_reference->price|string_format:"%.2f"}}</td>
            <td>{{$curr_reference->_unit_price|string_format:"%.2f"}}</td>
            <td><a href="#1" onclick="actionOrderItem({{$order->_id}}, 'add', {{$curr_reference->_id}})">Ajouter</a></td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="5">Aucune réference pour ce produit</td>
          </tr>
        {{/foreach}}
        </tbody>
      {{foreachelse}}
        <tr>
          <td colspan="5">Aucun produit dans cette catégorie</td>
        </tr>
      {{/foreach}}
      </table>
    {{/if}}
    </td>

    <td class="halfPane"><h3>Commandes</h3>
      <div id="orders_list"></div>
    </td>
  </tr>
</table>