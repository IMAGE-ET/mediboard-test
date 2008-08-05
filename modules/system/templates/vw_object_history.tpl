<table class="tbl">
  {{foreach from=$logs item="log"}}
  <tr>
    <td>{{mb_value object=$log field=type}}</td>
    <td>{{mb_value object=$log field=date}}</td>
    <td>{{$log->_ref_user->_view}}</td>
  </tr>
  {{/foreach}}
</table>