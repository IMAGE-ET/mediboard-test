{{assign var=segment value=$component->getSegment()}}
{{assign var=path value=$component->getPath()}}

<span class="field-name {{if $component->invalid}}invalid{{/if}}">{{$segment->name}}.{{"."|implode:$path}}</span>
<span class="field-description">{{$component->description}}</span>
<span class="type">{{$component->getTypeTitle()}}</span>
            
{{if $component->table}}
  <span class="table">{{$component->table}}</span>
{{/if}}
  
{{if $component->props instanceof CHL7v2DataTypeComposite}}
  {{if $component->children|@count > 0}}
    <ol>
      {{foreach from=$component->children key=i item=_child}}
        <li>
          {{mb_include module=hl7 template=inc_tree_component component=$_child}}
        </li>
      {{/foreach}}
    </ol>
  {{/if}}
{{else}}
  <span class="value">{{$component->data}}</span>
{{/if}}