{{if $show_label}}
  <table class="main layout">
    <tr>
      <td class="narrow input-label">{{mb_label object=$ex_object field=$_field_name}}</td>
      <td style="text-align: right;">
        {{$ex_field->prefix}}
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
        {{$ex_field->suffix}}
      </td>
    </tr>
  </table>
{{else}}
  {{$ex_field->prefix}}
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
  {{$ex_field->suffix}}
{{/if}}
