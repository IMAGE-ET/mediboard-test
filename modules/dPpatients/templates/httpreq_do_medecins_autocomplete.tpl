<ul>
  {{foreach from=$matches item=match}}
    {{assign var=view value=$match->_view}}
    <li id="match-{{$match->_id}}">
	    <strong class="view">{{$view|lower|replace:$keywords:"<em>$keywords</em>"}}</strong><br />
	    <small>{{$match->cp}} {{$match->ville}} - {{$match->disciplines|@truncate:25}}</small>
    </li>
  {{/foreach}}
</ul>