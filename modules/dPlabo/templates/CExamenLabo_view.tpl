<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Catalogue:</strong>
      <i>{{$object->_ref_catalogue_labo->_view}}</i>
      <br />
      <strong>Type:</strong>
      <i>{{mb_value object=$object field="type"}} ({{$object->_reference_values}})</i>
    </td>
  </tr>
</table>