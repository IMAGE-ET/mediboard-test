{{if $view_field == 1}}
  {{assign var=f value=$field}}
{{else}}
  {{assign var=f value=$view_field}}
{{/if}}
<ul>
{{foreach from=$matches item=match}}
  <li id="{{$match->_id}}">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|replace:$input:"<em>$input</em>"}}{{/if}}</li>
{{foreachelse}}
  <li><span class="informal">Aucun résultat</span></li>
{{/foreach}}
</ul>