<ol>
{{foreach from=$problems item=_problem}}
  <li>
  	<a href="?m=dPcompteRendu&amp;a=edit_compte_rendu&amp;compte_rendu_id={{$_problem->_id}}">
			{{$_problem}}
  	</a>
  </li>
{{/foreach}}
</ol>