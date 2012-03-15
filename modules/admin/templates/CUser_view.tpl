{{if $object->_ref_mediuser->_id}}
  {{mb_include module=mediusers template=CMediusers_view object=$object->_ref_mediuser}}
{{else}}
  {{mb_include template=CMbObject_view}}
{{/if}}
