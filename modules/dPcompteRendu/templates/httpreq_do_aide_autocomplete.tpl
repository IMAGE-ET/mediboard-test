<ul>
  {{foreach from=$object->_aides.$property item=aidesByDepend key=depend}}
  {{foreach from=$aidesByDepend item=aidesByOwner key=owner}}
  {{foreach from=$aidesByOwner item=name key=text}}
  {{if $depend != "no_enum" || !$dependField}}
    <li>
      <span style="float:right">{{$owner}}</span>
      {{if $depend && $dependField}}
      <div class="depend" style="display:none">{{$depend}}</div>
      <strong>
        {{tr}}{{$object->_class_name}}.{{$dependField}}.{{$depend}}{{/tr}}
      </strong> :
      {{/if}}
      <span>{{$name|lower|replace:$needle:"<em>$needle</em>"}}</span>
      <br/>
      <small class="text">{{$text|lower|replace:$needle:"<em>$needle</em>"}}</small>
    </li>
  {{/if}}
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</ul>