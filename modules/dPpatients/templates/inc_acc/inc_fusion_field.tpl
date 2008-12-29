{{assign var=specs value=$object1->_specs}}
{{assign var=specType value=$specs.$field->getSpecType()}}
<tr {{if $object1->$field != $object2->$field}}class="unequal"{{/if}}>
  <th>{{mb_label object=$object1 field=$field}}</th>
  <td class="{{$object1->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{if $specType != "enum" && $specType != "bool"}}{{mb_value object=$object1 field=$field}}{{else}}{{$object1->$field}}{{/if}}" checked="checked"
      onclick="setField('{{$field}}', $V(this), this.form.name);" />
      {{if $object1->$field != null}}
        {{mb_value object=$object1 field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td class="{{$object2->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{if $specType != "enum" && $specType != "bool"}}{{mb_value object=$object2 field=$field}}{{else}}{{$object2->$field}}{{/if}}"
      onclick="setField('{{$field}}', $V(this), this.form.name);" />
      {{if $object2->$field != null}}
        {{mb_value object=$object2 field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td class="{{$object_final->_props.$field}}">
    {{mb_field object=$object_final field=$field tabindex=$i form="editFrm" register="1" defaultOption="- Non spécifié -"}}
  </td>
</tr>