{{* Real time field
  $object : Object with a real time field
  $form : Form name
  $field : Real field name 
  $submit : Ajax submit callback name
  $timing : +10/-10 minutes array for selector
	$width : %
*}}

<td class="button" {{if @$width}}style="width: {{$width}}%"{{/if}}>
  {{if $object->$field}}
    {{mb_label object=$object field=$field}}
    {{if $can->edit}}
      {{mb_field object=$object field=$field form=$form onchange="$submit(this.form);"}}
<!-- 
      <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>
  -->
    {{elseif $modif_operation}}
      <select name="{{$field}}" onchange="{{$submit}}(this.form);">
        <option value="">-</option>
        {{foreach from=$timing.$field item=curr_time}}
        <option value="{{$curr_time}}" {{if $curr_time == $object->$field}}selected="selected"{{/if}}>
          {{$curr_time|date_format:$dPconfig.time}}
        </option>
        {{/foreach}}
      </select>
      <button type="button" class="cancel notext" onclick="$V(this.form.{{$field}}, '', true);">{{tr}}Cancel{{/tr}}</button>
    {{else}}
      {{$object->$field|date_format:$dPconfig.time}}
    {{/if}}
  
  {{elseif $can->edit || $modif_operation}}
    <input type="hidden" name="{{$field}}" value="" onchange="{{$submit}}(this.form);" />
    <button type="button" class="submit" onclick="$V(this.form.{{$field}}, 'current', true);">
    {{mb_label object=$object field=$field}}
    </button>
  {{else}}
    -
  {{/if}}
</td>
