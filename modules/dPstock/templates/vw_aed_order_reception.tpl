<script type="text/javascript">

function refreshOrderItem(item_id) {
  url = new Url;
  url.setModuleAction("dPstock", "httpreq_vw_order_reception_item");
  url.addParam("item_id", item_id);
  url.requestUpdate("item-"+item_id, { waitingText: null } );
}

function submitOrderItem (oForm, item_id) {
  submitFormAjax(oForm, 'systemMsg',{onComplete: function() {refreshOrderItem(item_id)} });
}

function submitOrder (oForm, order_id) {
  submitFormAjax(oForm, 'systemMsg',{
    onComplete: function() {
    {{foreach from=$order->_ref_order_items item=curr_item}}
      refreshOrderItem({{$curr_item->_id}});
    {{/foreach}}
    } 
  });
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
    {{if !$order->received}}
    <form name="form-order-{{$order->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_order_aed" />
      <input type="hidden" name="order_id" value="{{$order->_id}}" />
      <input type="hidden" name="_received" value="1" />
      <button type="button" class="cancel" onclick="submitOrder(this.form, {{$order->_id}})">Recevoir entièrement</button>
    </form>
    {{/if}}
      <h3>{{$order->_ref_societe->_view}} - {{$order->_view}}</h3>
      <table class="tbl" id="order-{{$order->_id}}">
        <tr>
          <th>Produit</th>
          <th>PU</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>Reçu</th>
        </tr>
        {{foreach from=$order->_ref_order_items item=curr_item}}
        <tbody id="item-{{$curr_item->_id}}">
          {{include file="inc_vw_order_reception_item.tpl"}}
        </tbody>
        {{/foreach}}
        <tr>
          <td colspan="8" style="border-top: 1px solid #666; text-align: right;">Total : {{$order->_total|string_format:"%.2f"}}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>