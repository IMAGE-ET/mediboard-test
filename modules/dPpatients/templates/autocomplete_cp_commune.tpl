<ul>
  {{foreach from=$matches item=_match}}
    <li>
	    <span class='cp'><strong>{{$_match.code_postal|emphasize:$keyword}}</strong></span>
	    &ndash;
	    <span class='commune'>{{$_match.commune|lower|capitalize|emphasize:$keyword}}</span>
      <div style="color: #888; padding-left: 1em;"><small>{{$_match.departement|lower|capitalize}}</small></div>
		</li>
  {{/foreach}}
</ul>