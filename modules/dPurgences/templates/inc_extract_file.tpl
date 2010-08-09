<table class="tbl">
  {{foreach from=$_passage->_ref_files item=_file}}
  <tr>
    <td>
      <a href="#" class="action" 
         onclick="File.popup('{{$_passage->_class_name}}','{{$_passage->_id}}','{{$_file->_class_name}}','{{$_file->_id}}');"
         onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectViewHistory')">
        {{$_file->file_name}}
      </a>
      <small>({{$_file->_file_size}})</small>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td>
      <em>
        {{tr}}{{$_passage->_class_name}}{{/tr}} :
        {{tr}}CFile.none{{/tr}}
      </em>
    </td>
  </tr>
  {{/foreach}}
</table>