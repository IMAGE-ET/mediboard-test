
{{foreach from=$segment_group->children item=_child}}
  <li>
    {{if $_child instanceof CHL7v2Segment}}
      <strong class="field-description">{{$_child->description}}</strong> 
      <span class="type">{{$_child->name}}</span>
      
      <ol>
        {{foreach from=$_child->fields key=_field_pos item=_field}}
          {{assign var=_field_pos value=$_field_pos+1}}
          
          <li>
            <span class="field-name">{{$_child->name}}-{{$_field_pos}}</span>
            <span class="field-description">{{$_field->description}}</span>
            <span class="type">{{$_field->datatype}}[{{$_field->length}}]</span>
            
            <ol>
              {{foreach from=$_field->items key=_i item=_item}}
                <li>
                  {{mb_include module=hl7 template=inc_tree_field_item component=$_item}}
                </li>
              {{/foreach}}
            </ol>
          </li>
        {{/foreach}}
      </ol>
    {{else}}
      <ul>
        {{$_child->name}}
        {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_child}}
      </ul>
    {{/if}}
  </li>
{{/foreach}}
