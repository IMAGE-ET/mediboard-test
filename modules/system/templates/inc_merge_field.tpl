{{assign var=specs value=$result->_specs}}
{{assign var=specType value=$specs.$field->getSpecType()}}

<tr {{if array_key_exists($field, $unequal)}}class="unequal"{{/if}}>
  <th>{{mb_label object=$result field=$field}}</th>
  
  {{foreach from=$objects item=object name=object}}
  <td class="{{$object->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{if $specType != "enum" && $specType != "bool"}}{{mb_value object=$object field=$field}}{{else}}{{$object->$field}}{{/if}}" {{if $smarty.foreach.object.first}}checked="checked"{{/if}}
      onclick="setField('{{$field}}', $V(this), this.form.name);" />
      {{if $object->$field != null}}
        {{mb_value object=$object field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  {{/foreach}}
  
  <td class="{{$result->_props.$field}}">
    {{mb_field object=$result field=$field form="form-merge" register=true defaultOption="- Non spécifié -"}}
  </td>
</tr>