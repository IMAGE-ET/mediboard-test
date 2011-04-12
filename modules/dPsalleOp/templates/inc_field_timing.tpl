{{* Real time field
  $object : Object with a real time field
  $form : Form name
  $field : Real field name 
  $submit : Ajax submit callback name
	$width : %
*}}

<td class="button" {{if @$width}}style="width: {{$width}}%"{{/if}}>
  {{if $object->$field}}
    {{mb_label object=$object field=$field}}
    {{if $modif_operation}}
    
      {{mb_field object=$object field=$field form=$form onchange="$submit(this.form);"}}
    {{else}}
      {{$object->$field|date_format:$conf.time}}
    {{/if}}
  
  {{elseif $modif_operation}}
    <input type="hidden" name="{{$field}}" value="" onchange="{{$submit}}(this.form);" />
    <button type="button" class="submit" onclick="$V(this.form.{{$field}}, 'current', true);">
    {{mb_label object=$object field=$field}}
    </button>
  {{else}}
    -
  {{/if}}
</td>
