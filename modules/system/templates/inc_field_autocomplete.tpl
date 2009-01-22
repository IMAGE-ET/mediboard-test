{{if $view_field == 1}}
  {{assign var=f value=$field}}
{{else}}
  {{assign var=f value=$view_field}}
{{/if}}
<ul>
{{foreach from=$matches item=match}}
  <li id="{{$match->_id}}">{{$match->$f|replace:$input:"<em>$input</em>"}}</li>
{{foreachelse}}
  <li><span class="informal">Aucun résultat</span></li>
{{/foreach}}
</ul>