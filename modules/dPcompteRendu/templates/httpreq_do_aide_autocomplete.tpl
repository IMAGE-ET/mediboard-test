{{if @$object->_aides_all_depends.$property}}
<ul>
  {{foreach from=$object->_aides_all_depends.$property item=aidesByDepend1 key=owner}}
  {{foreach from=$aidesByDepend1 item=aidesByDepend2 key=depend1}}
  {{foreach from=$aidesByDepend2 item=aides key=depend2}}
  {{foreach from=$aides item=name key=text}}
    <li>
      <span style="float:right">{{$owner}}</span>
      <div class="depend" style="display:none">{{$depend1}}</div>
      <div class="depend2" style="display:none">{{$depend2}}</div>
      <strong>
        {{if $depend1}}{{tr}}{{$object->_class_name}}.{{$depend_field_1}}.{{$depend1}}{{/tr}}{{/if}}
        {{if $depend2}}{{tr}}{{$object->_class_name}}.{{$depend_field_2}}.{{$depend2}}{{/tr}}{{/if}}
      </strong>
		
      <span>{{$name|lower|replace:$needle:"<em>$needle</em>"}}</span>
      <br/>
      <small class="text">{{$text|lower|replace:$needle:"<em>$needle</em>"}}</small>
    </li>
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</ul>
{{/if}}