{{assign var="file" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{tr}}CFile{{/tr}} 
      '{{$file}}'
    </th>
  </tr>
  
  <tr>
    <td>
      <strong>{{mb_label object=$file field=file_date}}</strong> :
      {{mb_value object=$file field=file_date}}
	  </td>
	</tr>
	
	<tr>
	  <td>
      <strong>{{mb_label object=$file field=file_owner}}</strong> :
	    {{$file->_ref_file_owner}}
	  </td>
	</tr>
	
	<tr>
	  <td>
      <strong>{{mb_label object=$file field=file_size}}</strong> :
	    {{$file->_file_size}}
	  </td>
	</tr>
	
	<tr>
	  <td class="text">
      <strong>{{mb_label object=$file field=object_id}}</strong> :
	    {{$file->_ref_object}}
    </td>
  </tr>
</table>