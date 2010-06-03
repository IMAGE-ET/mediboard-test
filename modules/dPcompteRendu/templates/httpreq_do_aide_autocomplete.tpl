<ul>
  {{foreach from=$object->_aides_new item=_aide}}
    {{if $_aide->_owner == "user"}}
      {{assign var=owner_icon value="user"}}
    {{elseif $_aide->_owner == "func"}}
      {{assign var=owner_icon value="function"}}
    {{else}}
      {{assign var=owner_icon value="group"}}
    {{/if}}
    {{assign var=depend_value_1 value=$_aide->depend_value_1}}
    {{assign var=depend_value_2 value=$_aide->depend_value_2}}
    
    <li class="{{$owner_icon}}" title="{{$_aide->_ref_owner}}">
      <div class="depend1" style="display:none">{{$depend_value_1}}</div>
      <div class="depend2" style="display:none">{{$depend_value_2}}</div>
      <strong>
        {{if $depend_value_1}}{{tr}}{{$object->_class_name}}.{{$_aide->_depend_field_1}}.{{$depend_value_1}}{{/tr}} - {{/if}}
        {{if $depend_value_2}}{{tr}}{{$object->_class_name}}.{{$_aide->_depend_field_2}}.{{$depend_value_2}}{{/tr}} - {{/if}}
      </strong>
  	
      <span>{{$_aide->name|emphasize:$needle}}</span>
      <br/>
      
<!-- The carriage return is here to append a carriage return 
at the end of the selected element in the textareas. DO NOT REMOVE. -->
<small class="text" style="color: #666; margin-left: 1em;">{{$_aide->text|emphasize:$needle}}
</small>
      
    </li>
  {{foreachelse}}
    {{if !@$hide_empty_list}}
    <li>
      {{tr}}CAideSaisie.none{{/tr}}
<small class="text" style="display: none;">{{$needle}}
</small>
    </li>
    {{/if}}
  {{/foreach}}
</ul>
