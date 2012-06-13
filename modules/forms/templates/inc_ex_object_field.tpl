{{mb_default var=form value=editExObject}}
{{mb_default var=mode value=normal}}

{{assign var=_field_name value=$ex_field->name}}
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
  
{{if $mode == "normal" && $_spec instanceof CRefSpec}}
  <script type="text/javascript">
  Main.add(function(){
    var form = getForm("{{$form}}");
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "{{$_spec->class}}");
    url.addParam("field", "{{$_field_name}}");
    url.addParam("input_field", "_{{$_field_name}}_view");
    url.autoComplete(form.elements["_{{$_field_name}}_view"], null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field,selected){
        $V(field.form["{{$_field_name}}"], selected.getAttribute("id").split("-")[2]);
        if ($V(field.form.elements["_{{$_field_name}}_view"]) == "") {
          $V(field.form.elements["_{{$_field_name}}_view"], selected.down('.view').innerHTML);
        }
      }
    });
  });
  </script>
  <input type="text" class="autocomplete" name="_{{$_field_name}}_view" value="{{$ex_object->_fwd.$_field_name}}" size="30" />
  {{mb_field object=$ex_object field=$_field_name form=$form hidden=true}}
{{elseif $_spec instanceof CEnumSpec && $_spec->vertical}}
  <fieldset>
    {{mb_field object=$ex_object field=$_field_name register=true increment=true form=$form}}
  </fieldset>
{{elseif $ex_field->formula}}
  {{mb_field object=$ex_object field=$_field_name readonly=true style="font-weight: bold; background-color: #aaff56;" class="noresize" rows=5 title=$ex_field->_formula}}
  <button type="button" class="cancel notext" style="margin-left: -1px;" onclick="$V($(this).previous(),'')">Vider</button>
{{else}}
  {{mb_field object=$ex_object field=$_field_name register=true increment=true form=$form emptyLabel=" "}}
{{/if}}
