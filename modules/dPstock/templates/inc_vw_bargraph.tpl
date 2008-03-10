{{assign var="th_max"      value=$stock->order_threshold_max/100}}
{{assign var="value"       value=$stock->quantity/$th_max}}
{{assign var="th_critical" value=$stock->order_threshold_critical/$th_max}}
{{assign var="th_min"      value=$stock->order_threshold_min/$th_max-$th_critical}}
{{assign var="th_optimum"  value=$stock->order_threshold_optimum/$th_max-$th_critical-$th_min}}

<div style="background-color: #FFFFFF; height: 10px; min-width: 50px;">
  <div style="overflow: hidden; height: 70%" title="En stock : {{$stock->quantity}}">
    <div style="width: {{$value}}%; background: #888; height:100%;"></div>
  </div>
  <div style="background: #CCC; height: 30%" title="Maximum : {{$stock->order_threshold_max}}">
    <div style="width: {{$th_critical}}%; background: #F66; float:left; height:100%;" title="Critique : {{$stock->order_threshold_critical}}"></div>
    <div style="width: {{$th_min}}%; background: #FE6; float:left; height:100%;" title="Minimum : {{$stock->order_threshold_min}}"></div>
    <div style="width: {{$th_optimum}}%; background: #66ff99; float:left; height:100%;" title="Optimal : {{$stock->order_threshold_optimum}}"></div>
  </div>
</div>