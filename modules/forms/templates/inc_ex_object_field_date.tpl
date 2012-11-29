{{if $show_label}}
  <table class="main layout">
    <tr>
      <td class="narrow input-label">{{mb_label object=$ex_object field=$_field_name}}</td>
      <td style="text-align: right;">
        {{$ex_field->prefix}}
        <div style="display: inline-block;">
          <input type="checkbox" style="line-height: 16px; vertical-align: middle; margin-right: -3px;" checked
                 onclick="this.up().select('input').without(this).invoke(this.checked?'enable':'disable')" />
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
    <input type="checkbox" style="line-height: 16px; vertical-align: middle; margin-right: -3px;" checked
           onclick="this.up().select('input').without(this).invoke(this.checked?'enable':'disable')" />
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
