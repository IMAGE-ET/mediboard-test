<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Admission:</strong>
      <i>le {{$object->entree_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>Sortie:</strong>
      <i>le {{$object->sortie_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>Praticien:</strong>
      <i>{{$object->_ref_praticien->_view}}</i>
      <br />
      <strong>Etablissement:</strong>
      <i>{{$object->_ref_group->_view}}</i>
      {{if $object->rques}}
      <br />
      <strong>Remarques:</strong>
      <i>{{$object->rques|nl2br|truncate:80}}</i>
      {{/if}}
    </td>
  </tr>
</table>