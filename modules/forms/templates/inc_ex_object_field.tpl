{{assign var=_field_name value=$ex_field->name}}
{{assign var=_spec value=$ex_object->_specs.$_field_name}}
  
{{if $_spec instanceof CRefSpec}}
  <script type="text/javascript">
  Main.add(function(){
    var form = getForm("editExObject");
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
  {{mb_field object=$ex_object field=$_field_name form=editExObject hidden=true}}
{{elseif $_spec instanceof CEnumSpec && $_spec->vertical}}
  <fieldset>
    {{mb_field object=$ex_object field=$_field_name register=true increment=true form=editExObject}}
  </fieldset>
{{elseif $ex_field->formula}}
  {{mb_field object=$ex_object field=$_field_name readonly=true}}
{{else}}
  {{mb_field object=$ex_object field=$_field_name register=true increment=true form=editExObject defaultOption=" &mdash; "}}
{{/if}}
