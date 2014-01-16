{{if $require_check_list}}
  <table class="main layout">
  {{foreach from=$daily_check_lists item=check_list}}
    <td>
      <h2>{{$check_list->_ref_list_type->title}}</h2>
      {{if $check_list->_ref_list_type->description}}
        <p>{{$check_list->_ref_list_type->description}}</p>
      {{/if}}

      {{mb_include module=salleOp template=inc_edit_check_list
      check_list=$check_list
      check_item_categories=$check_list->_ref_list_type->_ref_categories
      personnel=$listValidateurs
      list_chirs=$listChirs
      list_anesths=$listAnesths
      }}
    </td>
  {{/foreach}}
  </table>
{{else}}
  <div class="small-info">Aucune checklist à valider</div>
{{/if}}
