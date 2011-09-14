
{{foreach from=$segment_group->children item=_child}}
  <li>
    {{if $_child instanceof CHL7v2Segment}}
      <strong class="field-description">{{$_child->description}}</strong> <span class="type">{{$_child->name}}</span>
      
      <ol>
        {{foreach from=$_child->fields key=_field_pos item=_field}}
          {{assign var=_field_pos value=$_field_pos+1}}
          
          <li>
            <span class="field-name">{{$_child->name}}-{{$_field_pos}}</span>
            <span class="field-description">{{$_field->description}}</span>
            <span class="type">{{$_field->datatype}}</span>
            
            <ol>
              {{foreach from=$_field->items key=_i item=_item}}
                {{assign var=_item_pos value=$_i+1}}
                
                <li>
                  <div class="field-item">
                    {{if $_item->components|@is_array}}
                      {{if $_item->components|@count > 0}}
                        <ol>
                          {{foreach from=$_item->components key=_j item=_component}}
                            {{assign var=_component_pos value=$_j+1}}
                            
                            <li>
                              <span class="field-name">{{$_child->name}}-{{$_field_pos}}-{{$_component_pos}}</span>
                              
                              {{if $_component|@is_array}}
		                            {{assign var=comp_spec_meta value=$_item->composite_specs->components.$_j}}
		                            {{assign var=comp_spec value=$comp_spec_meta->getSpecs()}}
																
                                <span class="field-description">{{$_item->specs->elements->field.$_j->description}}</span>
                                <span class="type">{{$_item->specs->elements->field.$_j->datatype}} [{{$comp_spec_meta->getLength()}}] </span>
                                
                                {{if $_component|@count > 0}}
                                  <ol>
                                    {{foreach from=$_component key=_k item=_sub_component}}
                                      {{assign var=_sub_component_pos value=$_k+1}}
                                      
                                      <li>
                                        <span class="field-name">{{$_child->name}}-{{$_field_pos}}-{{$_component_pos}}-{{$_sub_component_pos}}</span>
                                        <span class="value">{{$_sub_component}}</span>
                                        <span class="field-description">{{$comp_spec->elements->field.$_k->description}}</span>
                                        <span class="type">{{$comp_spec->elements->field.$_k->datatype}} [{{$comp_spec_meta->components.$_k->getLength()}}] </span>
                                      </li>
                                    {{/foreach}}
                                  </ol>
                                {{/if}}
                              {{else}}
                                <span class="value">{{$_component}}</span>
                                <span class="field-description">{{$_item->specs->elements->field.$_j->description}}</span>
                                <span class="type">{{$_item->specs->elements->field.$_j->datatype}}</span>
                              {{/if}}
                            </li>
                          {{/foreach}}
                        </ol>
                      {{/if}}
                    {{else}}
                      <span class="value">{{$_item->components}}</span>
                      <span class="field-description">{{$_item->specs->description}}</span>
                      <span class="type">{{$_item->composite_specs->getType()}} [{{$_item->composite_specs->getLength()}}]</span>
                    {{/if}}
                  </div>
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
