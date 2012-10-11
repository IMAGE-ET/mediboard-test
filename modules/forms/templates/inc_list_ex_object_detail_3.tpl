
<table class="main tbl">
  {{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
    <tr>
      <td class="text">
        {{if $_ex_objects|@count}}
          <h2>{{$ex_classes.$_ex_class_id->name}}</h2>
          {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
            <h3>
              {{mb_value object=$_ex_object->_ref_first_log field=date}} - 
              {{mb_value object=$_ex_object->_ref_first_log field=user_id}}
            </h3>
            {{mb_include module=forms template=inc_vw_ex_object ex_object=$_ex_object hide_empty_groups=true}}
            <hr />
          {{/foreach}}
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">Aucun formulaire</td>
    </tr>
  {{/foreach}}
</table>