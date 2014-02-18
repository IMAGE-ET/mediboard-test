<table class="tbl">
  {{foreach from=$_passage->_ref_files item=_file}}
  <tr>
    <td>
      <a target="_blank" href="?m=files&a=fileviewer&suppressHeaders=1&force_dl=1&file_id={{$_file->_id}}" class="button download notext"></a>
      <a href="#" class="action" 
         onclick="File.popup('{{$_passage->_class}}','{{$_passage->_id}}','{{$_file->_class}}','{{$_file->_id}}');"
         onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectViewHistory')">
        {{$_file->file_name}}
      </a>
      <small>({{$_file->_file_size}})</small>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">
      {{tr}}{{$_passage->_class}}{{/tr}} :
      {{tr}}CFile.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>