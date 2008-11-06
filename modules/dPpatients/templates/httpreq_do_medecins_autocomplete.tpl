<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}">
    <span><strong>{{$match->nom}} {{$match->prenom}}</strong></span><br />
    <span>{{$match->cp}} {{$match->ville}} - {{$match->disciplines}}</span>
    </li>
  {{/foreach}}
</ul>