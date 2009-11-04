<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}">
	    <strong class="view">{{$match->_view|emphasize:$keywords}}</strong><br />
	    <small>{{$match->cp}} {{$match->ville}} - {{$match->disciplines|@truncate:25}}</small>
    </li>
  {{/foreach}}
</ul>