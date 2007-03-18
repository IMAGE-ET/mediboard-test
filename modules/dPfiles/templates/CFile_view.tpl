{{assign var="file" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$file->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date de création :</strong>
	  {{$file->file_date|date_format:"%d %B %Y à %H:%M:%S"}}
	  <br />
      <strong>Propriétaire :</strong>
	  {{$file->_ref_file_owner->_view}}
	  <br />
      <strong>Poids :</strong>
	  {{$file->_file_size}}
	  <br />
      <strong>Associé à :</strong>
	  {{$file->_ref_object->_view}}
	  <br />
    </td>
  </tr>
</table>