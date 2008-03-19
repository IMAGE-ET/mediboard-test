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
  submitFormAjax(oForm, 'systemMsg', {onComplete: function() {refreshOrder({{$order->_id}})} });
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      {{include file="inc_category_selector.tpl"}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id=0">
        Nouvelle commande
      </a>

    {{if $category->category_id}}
    <div style="text-align: right;">
    <button type="button" class="down" onclick="">Suggérer</button>
    <form name="product-order_autofill-{{$order->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_aed" />
      <input type="hidden" name="order_id" value="{{$order->_id}}" />
      <input type="hidden" name="_autofill" value="1" />
      <button type="button" class="change" onclick="submitOrderItem(this.form, {{$order->_id}})">Commande auto</button>
    </form>
    </div>
      <table class="tbl">
        <tr>
          <th>Produit</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>P.U.</th>
          <th style="width: 1%;"></th>
        </tr>
        
        <!-- Products list -->
        {{foreach from=$category->_ref_products item=curr_product}}
        
        <!-- Références list of this Product -->
        {{foreach from=$curr_product->_ref_references item=curr_reference}}
          {{if $curr_reference->societe_id == $order->societe_id}}
          <tr {{if $curr_reference->_id == $order->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab=vw_idx_order&amp;reference_id={{$curr_reference->_id}}" title="Voir ou modifier la référence">{{$curr_product->_view}}</a></td>
            <td>{{mb_value object=$curr_reference field=quantity}}</td>
            <td>{{mb_value object=$curr_reference field=price}}</td>
            <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
            <td>
            
            <form name="product-reference-{{$curr_reference->_id}}" action="?" method="post">
              <input type="hidden" name="m" value="dPstock" />
              <input type="hidden" name="dosql" value="do_order_item_aed" />
              <input type="hidden" name="order_item_id" value="0" />
              <input type="hidden" name="order_id" value="{{$order->_id}}" />
              <input type="hidden" name="reference_id" value="{{$curr_reference->_id}}" />
              <input type="text" name="quantity" value="1" size="2" />
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
      {{foreachelse}}
        <tr>
          <td colspan="5">Aucun produit dans cette catégorie</td>
        </tr>
      {{/foreach}}
      </table>
    {{/if}}
    </td>

    <td class="halfPane">
      <h3>Commande chez <i>{{$order->_ref_societe->_view}}</i></h3>
      <div id="orders_list">
      {{include file="inc_order.tpl"}}
      </div>
    </td>
  </tr>
</table>