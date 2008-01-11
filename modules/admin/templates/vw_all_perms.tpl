<table class="tbl">
  <tr>
    <td />
    {{foreach from=$listModules item=curr_mod}}
    <th>{{tr}}module-{{$curr_mod->mod_name}}-court{{/tr}}</th>
    {{/foreach}}
  </tr>
  {{foreach from=$listFunctions item=curr_func}}
  <tr>
    <th  style="background-color: #{{$curr_func->color}}; color: #000;">{{$curr_func->_view}}</th>
    <td colspan="{{$listModules|@count}}" style="background-color: #{{$curr_func->color}};" />
  </tr>
  {{foreach from=$curr_func->_ref_users item=curr_user}}
  {{assign var=user_id value=$curr_user->_id}}
  <tr>
    <td>{{$curr_user->_view}}</td>
    {{foreach from=$listModules item=curr_mod}}
    {{assign var=mod_id value=$curr_mod->_id}}
    <td class="button">
      {{$matrice.$user_id.$mod_id}}
    </td>
    {{/foreach}}
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>