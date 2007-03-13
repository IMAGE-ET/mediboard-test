{{assign var="sejour" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$sejour->_view}}
    </th>
  </tr>

  {{if $sejour->annule == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    SEJOUR ANNULE
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>
      <strong>Admission:</strong>
      <i>le {{$sejour->entree_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>Sortie:</strong>
      <i>le {{$sejour->sortie_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>Praticien:</strong>
      <i>{{$sejour->_ref_praticien->_view}}</i>
      <br />
      <strong>Etablissement:</strong>
      <i>{{$sejour->_ref_group->_view}}</i>
      {{if $sejour->rques}}
      <br />
      <strong>Remarques:</strong>
      <i>{{$sejour->rques|nl2br|truncate:50}}</i>
      {{/if}}
    </td>
  </tr>
</table>