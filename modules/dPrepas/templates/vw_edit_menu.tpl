<table class="main">
  <tr>
    <td colspan="2">
      <form name="FrmTypeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="typeVue" onchange="this.form.submit();">
        <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>{{tr}}CMenu-msg-typevue{{/tr}}</option>
        <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>{{tr}}CPlat-msg-typevue{{/tr}}</option>
        <option value="2" {{if $typeVue == 2}}selected="selected"{{/if}}>{{tr}}CTypeRepas-msg-typevue{{/tr}}</option>
      </select>
      </form><br />
    </td>
  </tr>
  {{if $typeVue==2}}
    {{include file="inc_vw_edit_typerepas.tpl"}}
  {{elseif $typeVue==1}}
    {{include file="inc_vw_edit_plats.tpl"}}  
  {{else}}
    {{include file="inc_vw_edit_menu.tpl"}}
  {{/if}}
</table>