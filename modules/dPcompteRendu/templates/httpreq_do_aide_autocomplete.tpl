<ul>
  {{foreach from=$aides item=aidesByDepend key=depend}}
  {{foreach from=$aidesByDepend item=aidesByOwner key=owner}}
  {{foreach from=$aidesByOwner item=name key=text}}
    <li>
      <span style="float:right">{{$owner}}</span>
      <strong class="depend">{{if $depend != "no_enum"}}{{$depend}}{{/if}}</strong> :
      <span>{{$name|lower|replace:$needle:"<em>$needle</em>"}}</span>
      <br/>
      <small class="text">{{$text|lower|replace:$needle:"<em>$needle</em>"}}</small>
    </li>
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</ul>