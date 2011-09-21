
{{if $component->props instanceof CHL7v2DataTypeComposite}}
  {{if $component->children|@count > 0}}
    Item {{$_i+1}}
    <ul class="field-item">
      {{foreach from=$component->children key=i item=_child}}
        <li>
          {{mb_include module=hl7 template=inc_tree_component component=$_child}}
        </li>
      {{/foreach}}
    </ul>
  {{/if}}
{{else}}
  <span class="value">{{$component->data}}</span>
{{/if}}
