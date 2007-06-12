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
    {{tr}}CSejour-annule{{/tr}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>
      <strong>{{tr}}CSejour-entree_prevue-court{{/tr}}:</strong>
      <i>le {{$sejour->entree_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>{{tr}}CSejour-sortie_prevue-court{{/tr}}:</strong>
      <i>le {{$sejour->sortie_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>{{tr}}CSejour-praticien_id{{/tr}}:</strong>
      <i>{{$sejour->_ref_praticien->_view}}</i>
      <br />
      <strong>{{tr}}CSejour-group_id{{/tr}}:</strong>
      <i>{{$sejour->_ref_group->_view}}</i>
      {{if $sejour->rques}}
      <br />
      <strong>{{tr}}CSejour-rques-court{{/tr}}:</strong>
      <i>{{$sejour->rques|nl2br|truncate:50}}</i>
      {{/if}}
    </td>
  </tr>
  <table class="form">
  {{assign var="vue" value="view"}}
  {{assign var="subject" value=$sejour}}
  {{include file="../../dPcabinet/templates/inc_list_actes.tpl"}}
  </table>
</table>