{{if @$object->_aides_all_depends.$property}}
<ul>
  {{assign var=aidesByDepend1 value=$object->_aides_all_depends.$property}}
  {{foreach from=$aidesByDepend1 key=depend1 item=aidesByDepend2 }}
  {{foreach from=$aidesByDepend2 key=depend2 item=aides }}
  {{foreach from=$aides item=_aide}}
    <li>
      <span style="float:right">{{mb_value object=$_aide field=_owner}}</span>
      <div class="depend" style="display:none">{{$depend1}}</div>
      <div class="depend2" style="display:none">{{$depend2}}</div>
      <strong>
        {{if $depend1}}{{tr}}{{$object->_class_name}}.{{$depend_field_1}}.{{$depend1}}{{/tr}} - {{/if}}
        {{if $depend2}}{{tr}}{{$object->_class_name}}.{{$depend_field_2}}.{{$depend2}}{{/tr}} - {{/if}}
      </strong>
		
      <span>{{$_aide->name|lower|replace:$needle:"<em>$needle</em>"}}</span>
      <br/>
      <small class="text" style="color: #666;">{{$_aide->text|lower|replace:$needle:"<em>$needle</em>"}}</small>
    </li>
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</ul>
{{/if}}