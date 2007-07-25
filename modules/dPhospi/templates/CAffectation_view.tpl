{{assign var="affectation" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
 
  <tr>
    <td>
      <strong>{{tr}}CAffectation-entree-court{{/tr}}:</strong>
      <i>Le {{$object->entree|date_format:"%d %B %Y"}}</i>
      <br />
      <strong>{{tr}}CAffectation-sortie-court{{/tr}}:</strong>
      <i>Au {{$object->sortie|date_format:"%d %B %Y"}}</i>
      <br />
      <strong>{{tr}}CAffectation-chambre{{/tr}}:</strong>
      <i>{{$object->_ref_lit->_view}}</i>
      <br />
    </td>
  </tr>
</table>