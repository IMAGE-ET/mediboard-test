{{mb_script module=salleOp script=check_list}}
{{mb_script module=salleOp script=check_list_group}}

<table class="main tbl" style="width: 50%;">
  <tr>
    <button type="button" class="new" onclick="CheckListGroup.duplicate();">
      {{tr}}CDailyCheckListGroup-title-duplicate{{/tr}}
    </button>
  </tr>
  <tr>
    <th>{{mb_title class=CDailyCheckListGroup field=title}}</th>
    <th>{{mb_title class=CDailyCheckListGroup field=description}}</th>
    <th class="narrow">{{mb_title class=CDailyCheckListGroup field=actif}}</th>
  </tr>

  {{foreach from=$check_list_groups item=_list_group}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="CheckListGroup.edit('{{$_list_group->_id}}');"></button>
        {{mb_value object=$_list_group field=title}}
      </td>
      <td class="compact">{{mb_value object=$_list_group field=description}}</td>
      <td>{{mb_value object=$_list_group field=actif}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="4" class="empty">{{tr}}CDailyCheckListGroup.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>