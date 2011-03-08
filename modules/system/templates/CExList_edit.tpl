{{mb_include module=system template=CMbObject_edit}}

{{if $object->_id}}
  {{mb_include module=system template=inc_ex_list_item_edit context=$object}}
{{/if}}