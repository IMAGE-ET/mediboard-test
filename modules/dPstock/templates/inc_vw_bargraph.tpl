{{assign var="th_max"      value=$stock->order_threshold_max/100}}
{{assign var="value"       value=$stock->quantity/$th_max}}
{{assign var="th_critical" value=$stock->order_threshold_critical/$th_max}}
{{assign var="th_min"      value=$stock->order_threshold_min/$th_max-$th_critical}}
{{assign var="th_optimum"  value=$stock->order_threshold_optimum/$th_max-$th_critical-$th_min}}



<div style="height: 0.8em; min-width: 5em;">
  <div style="overflow: hidden; height: 60%" title="En stock : {{$stock->quantity}}">
    <div style="width: {{$value}}%; background: 
{{if $value <= $th_critical}}
  {{$colors.0}}
{{elseif $value <= $th_min}}
  {{$colors.1}}
{{elseif $value <= $th_optimum}}
  {{$colors.2}}
{{else}}
  {{$colors.3}}
{{/if}}

; height:100%;"></div>
  </div>
  <div style="background: {{$colors.3}}; height: 40%; opacity: .3;" title="Maximum : {{$stock->order_threshold_max}}">
    <div style="background: {{$colors.0}}; width: {{$th_critical}}%; float:left; height:100%;" title="Critique : {{$stock->order_threshold_critical}}"></div>
    <div style="background: {{$colors.1}}; width: {{$th_min}}%;      float:left; height:100%;" title="Minimum : {{$stock->order_threshold_min}}"></div>
    <div style="background: {{$colors.2}}; width: {{$th_optimum}}%;  float:left; height:100%;" title="Optimal : {{$stock->order_threshold_optimum}}"></div>
  </div>
</div>