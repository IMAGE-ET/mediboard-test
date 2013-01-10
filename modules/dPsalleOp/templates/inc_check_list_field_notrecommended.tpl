<label style="white-space: nowrap;">
	<input type="radio" name="_items[{{$curr_type->_id}}]" value="nr" {{if $curr_type->_checked == "nr"}}checked="checked"{{/if}} onclick="submitCheckList(this.form, true)" />
	{{tr}}CDailyCheckItem.checked.nr{{/tr}}
</label>