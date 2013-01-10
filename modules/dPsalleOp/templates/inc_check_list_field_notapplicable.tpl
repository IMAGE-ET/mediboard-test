<label style="white-space: nowrap;">
  <input type="radio" name="_items[{{$curr_type->_id}}]" value="na" {{if $curr_type->_checked == "na"}}checked="checked"{{/if}} onclick="submitCheckList(this.form, true)" />
  {{tr}}CDailyCheckItem.checked.na{{/tr}}
</label>
