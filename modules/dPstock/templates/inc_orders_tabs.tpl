<script type="text/javascript">
Main.add(function(){
  tabs = Control.Tabs.create("orders-list");

  if (window.order_id) {
    tabs.setActiveTab("order-"+window.order_id);
  }
});
</script>

<ul class="control_tabs" id="orders-tabs">
{{foreach from=$list_orders item=_order}}
  <li>
    <a href="#order-{{$_order->_id}}">
      {{$_order->_ref_societe}} <br />
      <small>{{$_order->order_number}}</small> 
      <small class="count">({{$_order->_count.order_items}})</small>
    </a>
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$list_orders item=_order}}
  <div id="order-{{$_order->_id}}" style="display: none;">
    {{include file="inc_order.tpl" order=$_order}}
  </div>
{{/foreach}}