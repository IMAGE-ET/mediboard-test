{{assign var=disabled value=true}}
{{unique_id var=checkbox_uid}}

{{if $ex_object->_id}}
  {{assign var=disabled value=false}}
{{/if}}

{{if $show_label}}
  <table class="main layout">
    <tr>
      <td class="narrow input-label">{{mb_label object=$ex_object field=$_field_name}}</td>
      <td style="text-align: right;">
        {{$ex_field->prefix}}
        <div style="display: inline-block;">
          <input class="date-toggle" type="checkbox" checked id="cb-{{$checkbox_uid}}" onclick="ExObject.toggleDateField(this)" />
          {{mb_field
            object=$ex_object
            field=$_field_name
            register=true
            increment=true
            form=$form
            emptyLabel=" "
            style=$_style
            defaultstyle=1
            tabindex=$ex_field->tab_index
          }}
        </div>
        {{$ex_field->suffix}}
      </td>
    </tr>
  </table>
{{else}}
  {{$ex_field->prefix}}
  <div style="display: inline-block;">
    <input class="date-toggle" type="checkbox" checked id="cb-{{$checkbox_uid}}" onclick="ExObject.toggleDateField(this)" />
    {{mb_field
      object=$ex_object
      field=$_field_name
      register=true
      increment=true
      form=$form
      emptyLabel=" "
      style=$_style
      defaultstyle=1
      tabindex=$ex_field->tab_index
    }}
  </div>
  {{$ex_field->suffix}}
{{/if}}

{{if $disabled}}
<script type="text/javascript">
Main.add(function(){
  var cb = $("cb-{{$checkbox_uid}}");
  cb.checked = false;
  cb.onclick();
});
</script>
{{/if}}