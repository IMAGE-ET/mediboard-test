{{* Real time field
  $object : Object with a real time field
  $field : Real field name 
  $submit : Ajax submit callback name
  $timing : +10/-10 minutes array for selector
*}}

<td class="button">
  {{if $object->$field}}
  {{mb_label object=$object field=$field}}
  {{if $can->edit}}
  <input name="{{$field}}" size="3" maxLength="5" type="text" value="{{$object->$field|date_format:"%H:%M"}}" />
  <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>
  <button type="button" class="cancel notext" onclick="this.form.{{$field}}.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
  {{elseif $modif_operation}}
  <select name="{{$field}}" onchange="{{$submit}}(this.form));">
    <option value="">-</option>
    {{foreach from=$timing.$field item=curr_time}}
    <option value="{{$curr_time}}" {{if $curr_time == $object->$field}}selected="selected"{{/if}}>
      {{$curr_time|date_format:"%Hh%M"}}
    </option>
    {{/foreach}}
  </select>
  <button type="button" class="cancel notext" onclick="this.form.{{$field}}.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
  {{else}}
    {{$object->$field|date_format:"%Hh%M"}}
  {{/if}}
  {{elseif $can->edit || $modif_operation}}
  <input type="hidden" name="{{$field}}" value="" />
  <button type="button" class="submit" onclick="this.form.{{$field}}.value = 'current'; {{$submit}}(this.form);">
  {{mb_label object=$object field=$field}}
  </button>
  {{else}}-{{/if}}
</td>
