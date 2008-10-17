{{**
  * $stock ref|CProductStockGroup or ref|CProductStockService
  *}}
{{assign var=colors value=","|explode:"critical,min,optimum,max"}}
{{assign var=zone value=$stock->_zone}}

<div class="bargraph tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$stock->_class_name}}', object_id: '{{$stock->_id}}' } })">
  <div class="value {{$colors.$zone}}">
    <div class="{{$colors.$zone}}" style="width: {{$stock->_quantity}}%;"></div>
  </div>
  <div class="threshold{{if $stock->_quantity < $stock->_max}} {{$colors.3}}{{/if}}">
    <div class="{{$colors.0}}" style="width: {{$stock->_critical}}%;"></div>
    <div class="{{$colors.1}}" style="width: {{$stock->_min}}%;"></div>
    <div class="{{$colors.2}}" style="width: {{$stock->_optimum}}%;"></div>
    {{if $stock->_quantity > $stock->_max}}<div class="{{$colors.3}}" style="width: {{$stock->_max}}%;"></div>{{/if}}
  </div>
</div>
