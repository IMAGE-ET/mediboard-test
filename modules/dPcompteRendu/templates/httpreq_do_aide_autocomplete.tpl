{{if @$object->_aides_all_depends.$property}}
<ul>
  {{assign var=aidesByDepend1 value=$object->_aides_all_depends.$property}}
  {{foreach from=$aidesByDepend1 key=depend1 item=aidesByDepend2 }}
    {{foreach from=$aidesByDepend2 key=depend2 item=aides }}
      {{foreach from=$aides item=_aide}}
        {{if $_aide->_owner == "user"}}
          {{assign var=owner_icon value="user"}}
        {{elseif $_aide->_owner == "func"}}
          {{assign var=owner_icon value="user-function"}}
        {{else}}
          {{assign var=owner_icon value="group"}}
        {{/if}}
        <li>
          <img style="float:right;" 
               src="images/icons/{{$owner_icon}}.png" 
               title="{{$_aide->_ref_owner}}" />
               
          <div class="depend" style="display:none">{{$depend1}}</div>
          <div class="depend2" style="display:none">{{$depend2}}</div>
          <strong>
            {{if $depend1}}{{tr}}{{$object->_class_name}}.{{$depend_field_1}}.{{$depend1}}{{/tr}} - {{/if}}
            {{if $depend2}}{{tr}}{{$object->_class_name}}.{{$depend_field_2}}.{{$depend2}}{{/tr}} - {{/if}}
          </strong>
    		
          <span>{{$_aide->name|emphasize:$needle}}</span>
          <br/>
          
<!-- The carriage return is here to append a carriage return 
at the end of the selected element in the textareas. DO NOT REMOVE. -->
<small class="text" style="color: #666; margin-left: 1em;">{{$_aide->text|emphasize:$needle}}
</small>
    
        </li>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</ul>
{{/if}}