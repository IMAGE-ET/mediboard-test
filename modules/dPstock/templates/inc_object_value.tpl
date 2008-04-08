{{if $object->_class_name=="CProductStock" && $field=="bargraph"}}
  {{include file="inc_bargraph.tpl" stock=$object}}
{{else}}
  {{mb_value object=$object field=$field}}
{{/if}}