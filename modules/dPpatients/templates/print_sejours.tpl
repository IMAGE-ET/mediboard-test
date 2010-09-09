<table class="main" style="width: 100%">
  <tr><th>{{tr}}CSejour-entree_reelle{{/tr}}</th>
	  <th>{{tr}}CSejour-sortie_reelle{{/tr}}</th>
	  <th>{{tr}}CSejour-praticien_id-desc{{/tr}}</th></tr>
  {{foreach from=$sejours item=_sejour}}
    <tr>
      <td>{{$_sejour->entree_reelle}}</td>
      <td>{{$_sejour->sortie_reelle}}</td>
      <td>{{$_sejour->_ref_praticien->_view}}</td>
    </tr>
  {{/foreach}}
</table>