{{assign var="th_max"      value=$stock->order_threshold_max/100}}
{{assign var="value"       value=$stock->quantity/$th_max}}
{{assign var="th_critical" value=$stock->order_threshold_critical/$th_max}}
{{assign var="th_min"      value=$stock->order_threshold_min/$th_max-$th_critical}}
{{assign var="th_optimum"  value=$stock->order_threshold_optimum/$th_max-$th_critical-$th_min}}

<div class="bargraph">
  <div class="value" title="En stock : {{$stock->quantity}}">
    <div style="width: {{$value}}%;
background: 
{{if $value <= $th_critical}}
  {{$colors.0}}
{{elseif $value <= $th_critical+$th_min}}
  {{$colors.1}}
{{elseif $value <= $th_critical+$th_min+$th_optimum}}
  {{$colors.2}}
{{else}}
  {{$colors.3}}
{{/if}}
;"></div>
  </div>
  <div class="threshold" style="background: {{$colors.3}};" title="Maximum : {{$stock->order_threshold_max}}">
    <div style="background: {{$colors.0}}; width: {{$th_critical}}%;" title="Critique : {{$stock->order_threshold_critical}}"></div>
    <div style="background: {{$colors.1}}; width: {{$th_min}}%;"      title="Minimum : {{$stock->order_threshold_min}}"></div>
    <div style="background: {{$colors.2}}; width: {{$th_optimum}}%;"  title="Optimal : {{$stock->order_threshold_optimum}}"></div>
  </div>
</div>