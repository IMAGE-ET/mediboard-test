<table class="tbl">
  {{foreach from=$logs item="log"}}
  <tr style="text-align: center">
    <td>{{mb_ditto name=user value=$log->_ref_user->_view}}</td>
    <td>{{mb_ditto name=date value=$log->date|date_format:$dPconfig.date}}</td>
		<td>{{$log->date|date_format:$dPconfig.time}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td>{{tr}}CUserLog.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>