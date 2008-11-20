<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}">
    <span><strong class="view">{{$match->_view}}</strong></span><br />
    <span style="font-size: 0.8em;">{{$match->cp}} {{$match->ville}} - {{$match->disciplines|@truncate:25}}</span>
    </li>
  {{/foreach}}
</ul>