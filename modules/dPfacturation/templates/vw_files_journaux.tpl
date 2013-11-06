{{foreach from=$files item=_file}}
  {{assign var=object_class value=$_file->object_class}}
  {{assign var=object_id    value=$_file->object_id}}
  <tr id="tr_{{$_file->_guid}}">
    <td id="td_{{$_file->_guid}}">
      {{mb_include module=cabinet template="inc_widget_line_file"}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">
      {{tr}}CFile.none{{/tr}}
    </td>
  </tr>
{{/foreach}}