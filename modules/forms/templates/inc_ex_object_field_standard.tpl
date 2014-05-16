{{if $show_label}}
  <table class="main layout">
    <tr>
      <td class="narrow input-label">{{mb_label object=$ex_object field=$_field_name}}</td>
      <td style="text-align: right;">
        {{if $ex_class->pixel_positionning}}
          {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
        {{/if}}

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
            readonly=$field_readonly
            tabindex=$ex_field->tab_index
          }}

          {{mb_include module=forms template=inc_ex_field_link_formula ex_object=$ex_object ex_field=$ex_field spec=$_spec}}
        {{$ex_field->suffix}}
      </td>
    </tr>
  </table>
{{else}}
  {{if $ex_class->pixel_positionning}}
    {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
  {{/if}}

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
      readonly=$field_readonly
      tabindex=$ex_field->tab_index
    }}

    {{mb_include module=forms template=inc_ex_field_link_formula ex_object=$ex_object ex_field=$ex_field spec=$_spec}}
  {{$ex_field->suffix}}
{{/if}}
