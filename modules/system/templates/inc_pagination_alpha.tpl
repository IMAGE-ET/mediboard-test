{{* $change_page, $current *}}

{{assign var="letters" value="A"|range:"Z"}}

<div class="pagination {{if @$narrow}}narrow{{/if}}" style="min-height: 1em; white-space: nowrap;">
  <a href="#1" onclick="{{$change_page}}(''); $(this).addUniqueClassName('active'); return false;" class="page {{if $current == ""}}active{{/if}}">{{tr}}All{{/tr}}</a>
  {{foreach from=$letters item=letter}}
    <a href="#1" onclick="{{$change_page}}('{{$letter}}'); $(this).addUniqueClassName('active'); return false;" class="page {{if $current == $letter}}active{{/if}}">{{$letter}}</a>
  {{/foreach}}
  <a href="#1" onclick="{{$change_page}}('#'); $(this).addUniqueClassName('active'); return false;" class="page {{if $current == "#"}}active{{/if}}">#</a>
</div>