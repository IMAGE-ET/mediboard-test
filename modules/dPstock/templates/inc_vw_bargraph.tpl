<div class="bargraph">
  <div class="value" title="En stock : {{$stock->quantity}}">
    <div style="width: {{$stock->_quantity}}%; background: {{$colors.($stock->_zone)}};"></div>
  </div>
  <div class="threshold" {{if $stock->_quantity < $stock->_max}}style="background: {{$colors.3}};" title="Maximum : {{$stock->order_threshold_max}}"{{/if}}>
    <div style="background: {{$colors.0}}; width: {{stock->_critical}}%;" title="Critique : {{$stock->order_threshold_critical}}"></div>
    <div style="background: {{$colors.1}}; width: {{stock->_min}}%;" title="Minimum : {{$stock->order_threshold_min}}"></div>
    <div style="background: {{$colors.2}}; width: {{stock->_optimum}}%;" title="Optimal : {{$stock->order_threshold_optimum}}"></div>
    {{if $stock->_quantity > $stock->_max}}<div style="background: {{$colors.3}}; width: {{$stock->_max}}%;" title="Maximum : {{$stock->order_threshold_max}}"></div>{{/if}}
  </div>
</div>