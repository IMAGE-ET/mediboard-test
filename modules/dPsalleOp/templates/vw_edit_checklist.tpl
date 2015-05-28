<h2 style="text-align: center">{{if $salle->_id}}{{$salle->_view}}{{else}}{{$bloc->_view}}{{/if}}, le {{$date|date_format:$conf.date}}</h2>
{{if $nb_op_no_close}}
  <div class="small-warning">
    Il reste {{$nb_op_no_close}} opération(s) non terminée(s).
  </div>
{{/if}}

{{if $require_check_list}}
  <table class="main layout">
    {{foreach from=$daily_check_lists item=check_list}}
      <td>
        <h2>{{$check_list->_ref_list_type->title}}</h2>
        {{if $check_list->_ref_list_type->description}}
          <p>{{$check_list->_ref_list_type->description}}</p>
        {{/if}}
        <div id="check_list_{{$check_list->type}}_{{$check_list->list_type_id}}">
          {{mb_include module=salleOp template=inc_edit_check_list
          check_list=$check_list
          check_item_categories=$check_list->_ref_list_type->_ref_categories
          personnel=$listValidateurs
          list_chirs=$listChirs
          list_anesths=$listAnesths
          }}
        </div>
      </td>
    {{/foreach}}
  </table>
{{else}}
  <div class="small-info">Aucune checklist à valider</div>
{{/if}}