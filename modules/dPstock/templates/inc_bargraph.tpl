{{**
  * $stock ref|CProductStock
  *}}
{{assign var=colors value=","|explode:"#F00,#FC3,#1D6,#06F,#000"}}
{{assign var=zone value=$stock->_zone}}

<div class="bargraph">
  <div class="legend">
    <div class="value" style="background: {{$colors.$zone}};">{{tr}}CProductStock-quantity{{/tr}} : {{$stock->quantity}}</div>
    <div><div style="background: {{$colors.0}};" class="color"></div>{{tr}}CProductStock-order_threshold_critical{{/tr}} : {{$stock->order_threshold_critical}}</div>
    <div><div style="background: {{$colors.1}};" class="color"></div>{{tr}}CProductStock-order_threshold_min{{/tr}} : {{$stock->order_threshold_min}}</div>
    <div><div style="background: {{$colors.2}};" class="color"></div>{{tr}}CProductStock-order_threshold_optimum{{/tr}} : {{$stock->order_threshold_optimum}}</div>
    <div><div style="background: {{$colors.3}};" class="color"></div>{{tr}}CProductStock-order_threshold_max{{/tr}} : {{$stock->order_threshold_max}}</div>
  </div>
  <div class="value" style="border-color: {{$colors.$zone}};">
    <div style="width: {{$stock->_quantity}}%; background: {{$colors.$zone}};"></div>
  </div>
  <div class="threshold" {{if $stock->_quantity < $stock->_max}}style="background: {{$colors.3}};"{{/if}}>
    <div style="background: {{$colors.0}}; width: {{$stock->_critical}}%;"></div>
    <div style="background: {{$colors.1}}; width: {{$stock->_min}}%;"></div>
    <div style="background: {{$colors.2}}; width: {{$stock->_optimum}}%;"></div>
    {{if $stock->_quantity > $stock->_max}}<div style="background: {{$colors.3}}; width: {{$stock->_max}}%;"></div>{{/if}}
  </div>
</div>
