{{foreach from=$nodes item=_node}}
  {{$_node|smarty:nodefaults}}
{{foreachelse}}
  <div class="small-info">Aucun résultat</div>
{{/foreach}}
