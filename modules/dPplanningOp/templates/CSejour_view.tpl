{{assign var="sejour" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th class="title text">
      {{$sejour}}
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
      <strong>{{mb_label object=$sejour field=type}}:</strong>
      <i>{{mb_value object=$sejour field=type}}</i>
      <br />
      <strong>{{tr}}CSejour-entree_prevue-court{{/tr}}:</strong>
      <i>le {{$sejour->entree_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      <strong>{{tr}}CSejour-sortie_prevue-court{{/tr}}:</strong>
      <i>le {{$sejour->sortie_prevue|date_format:"%d %B %Y à %Hh%M"}}</i>
      <br />
      {{if $sejour->mode_sortie != ""}}
      <strong>{{tr}}CSejour-mode_sortie{{/tr}}:</strong>
      <i>{{tr}}CSejour.mode_sortie.{{$sejour->mode_sortie}}{{/tr}}</i>
      <br />
      {{/if}}
      <strong>{{tr}}CSejour-praticien_id{{/tr}}:</strong>
      <i>{{$sejour->_ref_praticien->_view}}</i>
      <br />
      {{if $sejour->libelle}}
      <strong>{{mb_label object=$sejour field=libelle}}:</strong>
      <i>{{mb_value object=$sejour field=libelle}}</i>
      <br />
      {{/if}}
      <strong>{{tr}}CSejour-group_id{{/tr}}:</strong>
      <i>{{$sejour->_ref_group->_view}}</i>
      {{if $sejour->rques}}
      <br />
      <strong>{{tr}}CSejour-rques-court{{/tr}}:</strong>
      <i>{{$sejour->rques|nl2br|truncate:50}}</i>
      {{/if}}
    </td>
  </tr>
</table>
<table class="tbl tooltip">
	{{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>