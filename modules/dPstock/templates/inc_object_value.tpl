{{if $object->_class_name=="CProductStockGroup" && $field=="bargraph"}}
  {{include file="inc_bargraph.tpl" stock=$object}}
{{else}}
  {{mb_value object=$object field=$field}}
{{/if}}