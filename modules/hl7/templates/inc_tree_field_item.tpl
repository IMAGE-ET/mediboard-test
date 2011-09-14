<div class="field-item">
  <span class="field-description">{{$component->description}}</span>
  <span class="type">{{$component->datatype}}[{{$component->length}}]</span>
  
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
</div>