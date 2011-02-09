<label style="white-space: nowrap; {{if $default_value == "yes"}}font-style: italic;{{/if}} {{if $default_value == 'no'}}font-weight: bold;{{/if}}">
  <input type="radio" name="_items[{{$curr_type->_id}}]" value="no" {{if $curr_type->_checked == "no" || ($curr_type->_checked === null && $default_value == "no")}}checked="checked"{{/if}} />
  {{tr}}CDailyCheckItem.checked.no{{/tr}}{{if $default_value == "yes"}}*{{/if}}
</label>
