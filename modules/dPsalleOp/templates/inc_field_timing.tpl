{{* Real time field
  $object : Object with a real time field
  $form : Form name
  $field : Real field name 
  $submit : Ajax submit callback name
  $width : %
*}}

{{mb_default var=disabled value=false}}
{{mb_default var=use_disabled value=false}}
{{if $use_disabled == "yes" && $conf.dPsalleOp.COperation.use_check_timing}}
  {{assign var=disabled value="yes"}}
{{/if}}

<td class="button" {{if @$width}}style="width: {{$width}}%"{{/if}}>
  {{if $object->$field}}
    {{mb_label object=$object field=$field}}
    {{if $modif_operation}}
      {{mb_field object=$object field=$field form=$form register=true onchange="$submit(this.form);"}}
    {{else}}
      {{$object->$field|date_format:$conf.time}}
    {{/if}}

    {{if $field == "fin_op" && $object instanceof COperation}}
      {{mb_include module=forms template=inc_widget_ex_class_register object=$object event_name=fin_intervention cssStyle="display: inline-block;"}}
    {{/if}}
  
  {{elseif $modif_operation}}
    <input type="hidden" name="{{$field}}" value="" onchange="{{$submit}}(this.form);"/>
    <input type="hidden" name="_set_{{$field}}" value="1" /> {{* Custom flag to tell we are setting the value (module formulaires) *}}
    <button type="button" class="submit" onclick="$V(this.form.{{$field}}, 'current', true);" {{if $disabled == "yes"}}disabled{{/if}}>
    {{mb_label object=$object field=$field}}
    </button>
  {{else}}
    -
  {{/if}}
</td>
