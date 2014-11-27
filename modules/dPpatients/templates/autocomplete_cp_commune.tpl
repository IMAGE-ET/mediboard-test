<ul>
  {{foreach from=$matches key=_key item=_match}}
    <li>
	    <span class='cp'><strong>{{$_match.code_postal|emphasize:$keyword}}</strong></span>
	    &ndash;
	    <span class='commune'>{{$_match.commune|emphasize:$keyword}}</span>
      <div style="color: #888; padding-left: 1em;"><small>{{if $_match.departement}}{{$_match.departement}} - {{/if}}{{$_match.pays}}</small></div>
		</li>
  {{/foreach}}
</ul>