{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date:</strong>
      <i>{{mb_value object=$object field="date"}}</i>
      <br />
      <strong>Commentaires:</strong>
      <i>{{mb_value object=$object field="commentaire"}}</i>
    </td>
  </tr>
</table>