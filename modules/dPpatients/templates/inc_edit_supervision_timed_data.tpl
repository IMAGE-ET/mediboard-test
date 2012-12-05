<script type="text/javascript">
  Main.add(function(){
    var item = $("list-{{$timed_data->_guid}}");
    if (item) {
      item.addUniqueClassName("selected");
    }
  });
</script>

<form name="edit-supervision-graph-timed-data" method="post" action="?m=dPpatients" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="@class" value="CSupervisionTimedData" />
  <input type="hidden" name="owner_class" value="CGroups" />
  <input type="hidden" name="owner_id" value="{{$g}}" />
  <input type="hidden" name="callback" value="SupervisionGraph.editTimedData" />
  {{mb_key object=$timed_data}}

  <table class="main form">
  {{mb_include module=system template=inc_form_table_header object=$timed_data colspan=7}}

    <tr>
      <th>{{mb_label object=$timed_data field=title}}</th>
      <td>{{mb_field object=$timed_data field=title}}</td>

      <th>{{mb_label object=$timed_data field=period}}</th>
      <td>{{mb_field object=$timed_data field=period typeEnum=select}} min</td>

      <th>{{mb_label object=$timed_data field=disabled}}</th>
      <td>{{mb_field object=$timed_data field=disabled typeEnum=checkbox}}</td>

      <td>
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>

      {{if $timed_data->_id}}
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'', objName:'{{$timed_data->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>
