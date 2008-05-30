<tr>
  <th>{{mb_label object=$object1 field=$field}}</th>
  <td class="{{$object1->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{$object1->$field}}" checked="checked"
      onclick="$V(this.form.{{$field}}, '{{$object1->$field|smarty:nodefaults|JSAttribute}}');" />
      {{if $object1->$field != null}}
        {{mb_value object=$object1 field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td class="{{$object2->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{$object2->$field}}"
      onclick="$V(this.form.{{$field}}, '{{$object2->$field|smarty:nodefaults|JSAttribute}}');" />
      {{if $object2->$field != null}}
        {{mb_value object=$object2 field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td class="{{$object_final->_props.$field}}">
    {{if $object_final->_props.$field=="birthDate"}}
      {{mb_field object=$object_final field=$field tabindex=$i readonly="readonly"}}
    {{else}}
      {{mb_field object=$object_final field=$field tabindex=$i form="editFrm" register="1" defaultOption="- Non spécifié -"}}
    {{/if}}
  </td>
</tr>