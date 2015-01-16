<div style="padding-left: 2px; margin: -1px;" data-aggregation="{{$match->agregation}}" data-types="{{$match->types}}">
  <span class="view">
    {{if $match->group_id == $group_id}}
      <img src="images/icons/group.png">
    {{/if}}
    {{if $match->function_id == $function_id}}
      <img src="images/icons/user-function.png">
    {{/if}}
    {{if $match->user_id == $user_id}}
      <img src="images/icons/user.png">
    {{/if}}
  </span>
  <span class="view">{{$match->entry}}</span>
  <span class="view">{{if $match->titre}} (titre : {{$match->titre}}){{/if}}</span>
</div>