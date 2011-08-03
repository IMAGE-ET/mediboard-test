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
      <div class="depend1" style="display:none">{{mb_value object=$_aide field=depend_value_1}}</div>
      <div class="depend2" style="display:none">{{mb_value object=$_aide field=depend_value_2}}</div>
      <strong>
        {{if $depend_value_1}}{{$_aide->_vw_depend_field_1}} - {{/if}}
        {{if $depend_value_2}}{{$_aide->_vw_depend_field_2}} - {{/if}}
      </strong>
    
      <span>{{$_aide->name|emphasize:$needle}}</span>
      <br/>
      
      <small class="text" style="color: #666; margin-left: 1em;">{{$_aide->text|emphasize:$needle}}</small>

      {{* Keep the one line formating here, white-space:pre is to keep new lines in IE *}}
      <div class="value" style="display: none; white-space: pre;">{{$_aide->text}}
</div>

    </li>
  {{foreachelse}}
    {{if !@$hide_empty_list}}
    <li>
      {{tr}}CAideSaisie.none{{/tr}}
<small class="value" style="display: none;">{{$needle}}
</small>
    </li>
    {{/if}}
  {{/foreach}}
</ul>
