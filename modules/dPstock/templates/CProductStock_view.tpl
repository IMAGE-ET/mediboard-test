{{**
  * $stock ref|CProductStock
  *}}
{{assign var=colors value=","|explode:"critical,min,optimum,max"}}
{{assign var=zone value=$object->_zone}}
<div class="legend">
  <div class="value {{$colors.$zone}}">{{tr}}CProductStock-quantity{{/tr}} : {{$object->quantity}}</div>
  <div><div class="color {{$colors.0}}"></div>{{tr}}CProductStock-order_threshold_critical{{/tr}} : {{$object->order_threshold_critical}}</div>
  <div><div class="color {{$colors.1}}"></div>{{tr}}CProductStock-order_threshold_min{{/tr}} : {{$object->order_threshold_min}}</div>
  <div><div class="color {{$colors.2}}"></div>{{tr}}CProductStock-order_threshold_optimum{{/tr}} : {{$object->order_threshold_optimum}}</div>
  <div><div class="color {{$colors.3}}"></div>{{tr}}CProductStock-order_threshold_max{{/tr}} : {{$object->order_threshold_max}}</div>
</div>
