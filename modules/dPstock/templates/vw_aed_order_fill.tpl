<script type="text/javascript">
function pageMain() {
  regFieldCalendar("edit_order", "date");
  PairEffect.initGroup("productToggle", { bStartVisible: true });
}

function refreshOrder(order_id) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate("orders_list", { waitingText: null } );
}

function submitOrderItem (oForm, order_id) {
  submitFormAjax(oForm, 'systemMsg',{onComplete: function() {refreshOrder(order_id)} });
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      {{include file="inc_vw_category_selector.tpl"}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id=0">
        Nouvelle commande
      </a>

    {{if $category->category_id}}
    <h3>{{$category->_view}}</h3>
      <table class="tbl">
        <tr>
          <th>{{if $unique_societe}}Produit{{else}}Fournisseur{{/if}}</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>P.U.</th>
          <th></th>
        </tr>
        
        <!-- Products list -->
        {{foreach from=$category->_ref_products item=curr_product}}
        {{if !$unique_societe}}
        <tr id="product-{{$curr_product->_id}}-trigger">
          <td colspan="5">
            {{$curr_product->_view}} ({{$curr_product->_ref_references|@count}} références)
          </td>
        </tr>
        <tbody class="productToggle" id="product-{{$curr_product->_id}}">
        {{/if}}
        
        <!-- Références list of this Product -->
        {{foreach from=$curr_product->_ref_references item=curr_reference}}
          {{if $curr_reference->societe_id == $order->societe_id}}
          <tr {{if $curr_reference->_id == $order->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab=vw_idx_order&amp;reference_id={{$curr_reference->_id}}" title="Voir ou modifier la référence">{{if $unique_societe}}{{$curr_product->_view}}{{else}}{{$curr_reference->_ref_societe->_view}}{{/if}}</a></td>
            <td>{{$curr_reference->quantity}}</td>
            <td>{{$curr_reference->price|string_format:"%.2f"}}</td>
            <td>{{$curr_reference->_unit_price|string_format:"%.2f"}}</td>
            <td>
            
            <form name="product-order-{{$order->_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPstock" />
              <input type="hidden" name="dosql" value="do_order_item_aed" />
              <input type="hidden" name="order_item_id" value="0" />
              <input type="hidden" name="order_id" value="{{$order->_id}}" />
              <input type="hidden" name="reference_id" value="{{$curr_reference->_id}}" />
              <button class="add notext" type="button" onclick="submitOrderItem(this.form, {{$order->_id}})">
                Ajouter
              </button>
            </form>
            </td>
          </tr>
          {{/if}}
        {{foreachelse}}
          <tr>
            <td colspan="5">Aucune réference pour ce produit</td>
          </tr>
        {{/foreach}}
        {{if !$unique_societe}}
        </tbody>
        {{/if}}
      {{foreachelse}}
        <tr>
          <td colspan="5">Aucun produit dans cette catégorie</td>
        </tr>
      {{/foreach}}
      </table>
    {{/if}}
    </td>

    <td class="halfPane"><h3>Commandes</h3>
      <div id="orders_list">
      {{include file="inc_vw_order.tpl"}}
      </div>
    </td>
  </tr>
</table>