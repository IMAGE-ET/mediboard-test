<label style="white-space: nowrap; {{if $check_list->object_class == "COperation"}}{{if $default_value == "yes"}}font-style: italic;{{/if}} {{if $default_value == 'no'}}font-weight: bold;{{/if}}{{/if}}}">
  <input type="radio" name="_items[{{$curr_type->_id}}]" value="no" {{if $curr_type->_checked == "no" || ($curr_type->_checked === null && $default_value == "yes")}}checked="checked"{{/if}} />
  {{tr}}CDailyCheckItem.checked.no{{/tr}}{{if $check_list->object_class == "COperation" && $default_value == "yes"}}*{{/if}}
</label>
