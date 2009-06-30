{{assign var=ref_field value="_ref_$field"}}

<tr {{if $object1->$field != $object2->$field}}class="unequal"{{/if}}">
  <th>{{mb_label object=$object1 field=$field}}</th>
  <td class="{{$object1->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{$object1->$field}}" checked="checked"
      onclick="$V(this.form.{{$field}}, '{{$object1->$field}}'); 
               $V(this.form._{{$field}}_view, '{{$object1->$ref_field->_view|smarty:nodefaults|JSAttribute}}')" />
      {{if $object1->$field != null}}
        {{$object1->$ref_field->_view}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td class="{{$object2->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{$object2->$field}}"
      onclick="$V(this.form.{{$field}}, '{{$object2->$field}}'); 
               $V(this.form._{{$field}}_view, '{{$object2->$ref_field->_view|smarty:nodefaults|JSAttribute}}')" />
      {{if $object2->$field != null}}
        {{$object2->$ref_field->_view}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td>
    {{mb_field object=$object_final field=$field hidden=1 prop=""}}
    <input type="text" readonly="readonly" size="30" name="_{{$field}}_view" value="{{$object_final->$ref_field->_view}}" />
  </td>
</tr>
