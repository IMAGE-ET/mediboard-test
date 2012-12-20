{{mb_default var=form value=editExObject}}
{{mb_default var=mode value=normal}}
{{mb_default var=is_predicate value=false}}

{{if !isset($ex_class|smarty:nodefaults)}}
  {{assign var=ex_class value=$ex_object->_ref_ex_class}}
{{else}}
  {{assign var=ex_object value=$ex_class->_ex_object}}
{{/if}}

{{assign var=show_label value=$ex_class->pixel_positionning}}
{{if $show_label}}
  {{assign var=show_label value=$ex_field->show_label}}
{{/if}}
{{if $is_predicate}}
  {{assign var=show_label value=false}}
{{/if}}

{{assign var=_field_name value=$ex_field->name}}
{{assign var=_properties value=$ex_field->_default_properties}}
{{assign var=_spec value=$ex_object->_specs.$_field_name}}

{{if $mode == "normal" && $ex_field->_triggered_data|@count}}
  <script type="text/javascript">
  Main.add(function(){
    var form = getForm("{{$form}}");
    {{if $_spec instanceof CSetSpec}}
      {{foreach from=$_spec->_list item=_value}}
        ExObject.initTriggers({{$ex_field->_triggered_data|@json}}, form, "_{{$_field_name}}_{{$_value}}", "{{$ex_object->_ref_ex_class->name}}", true);
      {{/foreach}}
    {{else}}
      ExObject.initTriggers({{$ex_field->_triggered_data|@json}}, form, "{{$_field_name}}", "{{$ex_object->_ref_ex_class->name}}");
    {{/if}}
  });
  </script>
{{/if}}

{{assign var=_style value=""}}
{{foreach from=$_properties key=_type item=_value}}
  {{if $_value != ""}}
    {{assign var=_style value="$_style $_type:$_value;"}}
  {{/if}}
{{/foreach}}

{{if $mode == "normal" && $_spec instanceof CRefSpec}}
  {{mb_include module=forms template=inc_ex_object_field_autocomplete}}

{{elseif ($_spec instanceof CEnumSpec && $_spec->typeEnum == "radio") || ($_spec instanceof CSetSpec && $_spec->typeEnum == "checkbox") || ($_spec instanceof CBoolSpec && $_spec->typeEnum == "radio")}}
  {{mb_include module=forms template=inc_ex_object_field_fieldset}}

{{elseif $ex_field->formula && !$is_predicate}}
  {{mb_include module=forms template=inc_ex_object_field_formula}}

{{elseif $_spec instanceof CSetSpec && $_spec->typeEnum == "select"}}
  {{mb_include module=forms template=inc_ex_object_field_select_multiple}}

{{elseif $_spec instanceof CTextSpec || ($_spec instanceof CEnumSpec && $_spec->typeEnum == "select")}}
  {{mb_include module=forms template=inc_ex_object_field_two_lines}}

{{elseif $ex_class->pixel_positionning && !$is_predicate && ($_spec instanceof CDateSpec || $_spec instanceof CDateTimeSpec || $_spec instanceof CTimeSpec)}}
  {{mb_include module=forms template=inc_ex_object_field_date}}

{{else}}
  {{mb_include module=forms template=inc_ex_object_field_standard}}
{{/if}}
