<label style="white-space: nowrap; {{if $check_list->object_class == "COperation"}}{{if $default_value == "no"}}font-style: italic;{{/if}} {{if $default_value == 'yes'}}font-weight: bold;{{/if}}{{/if}}">
  <input type="radio" name="_items[{{$curr_type->_id}}]" value="yes" {{if $curr_type->_checked == "yes" || ($curr_type->_checked === null && $default_value == "no")}}checked="checked"{{/if}} />
  {{tr}}CDailyCheckItem.checked.yes{{/tr}}{{if $check_list->object_class == "COperation" && $default_value == "no"}}*{{/if}}
</label>
