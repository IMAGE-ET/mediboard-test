<ul>
  {{foreach from=$matches item=match}}
    {{assign var=view value=$match->_view}}
    <li id="match-{{$match->_id}}">
      <strong class="view">{{$view|lower|replace:$keywords:"$keywords"}}</strong><br />
      <small>{{$match->code}} - {{$match->name}}  {{$match->description|@truncate:25}}</small>
    </li>
  {{/foreach}}
</ul>