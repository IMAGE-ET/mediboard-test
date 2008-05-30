{{if $field_name}}
  {{assign var=field1 value="_$field_name"|cat:"1"}}
  {{assign var=field2 value="_$field_name"|cat:"2"}}
  {{assign var=field3 value="_$field_name"|cat:"3"}}
  {{assign var=field4 value="_$field_name"|cat:"4"}}
  {{assign var=field5 value="_$field_name"|cat:"5"}}
{{else}}
  {{assign var=field1 value="_$field"|cat:"1"}}
  {{assign var=field2 value="_$field"|cat:"2"}}
  {{assign var=field3 value="_$field"|cat:"3"}}
  {{assign var=field4 value="_$field"|cat:"4"}}
  {{assign var=field5 value="_$field"|cat:"5"}}
{{/if}}
<tr>
  <th>{{mb_label object=$object_final field=$field}}</th>
  <td class="{{$object1->_props.$field}}">
    <label>
      <input type="radio" name="_choix_{{$field}}" value="{{$object1->$field}}" checked="checked"
      onclick="$V(this.form._{{$field}}1, '{{$object1->$field1|smarty:nodefaults|JSAttribute}}'); 
               $V(this.form._{{$field}}2, '{{$object1->$field2|smarty:nodefaults|JSAttribute}}');
               $V(this.form._{{$field}}3, '{{$object1->$field3|smarty:nodefaults|JSAttribute}}'); 
               $V(this.form._{{$field}}4, '{{$object1->$field4|smarty:nodefaults|JSAttribute}}'); 
               $V(this.form._{{$field}}5, '{{$object1->$field5|smarty:nodefaults|JSAttribute}}');" />
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
      onclick="$V(this.form._{{$field}}1, '{{$object2->$field1|smarty:nodefaults|JSAttribute}}'); 
               $V(this.form._{{$field}}2, '{{$object2->$field2|smarty:nodefaults|JSAttribute}}');
               $V(this.form._{{$field}}3, '{{$object2->$field3|smarty:nodefaults|JSAttribute}}'); 
               $V(this.form._{{$field}}4, '{{$object2->$field4|smarty:nodefaults|JSAttribute}}'); 
               $V(this.form._{{$field}}5, '{{$object2->$field5|smarty:nodefaults|JSAttribute}}');" />
      {{if $object2->$field != null}}
        {{mb_value object=$object2 field=$field}}
      {{else}}
        <span style="opacity: 0.3">Non spécifié</span>
      {{/if}}
    </label>
  </td>
  <td>
    {{mb_field object=$object_final field=$field1 tabindex=$i++ size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
    {{mb_field object=$object_final field=$field2 tabindex=$i++ size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
    {{mb_field object=$object_final field=$field3 tabindex=$i++ size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
    {{mb_field object=$object_final field=$field4 tabindex=$i++ size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
    {{mb_field object=$object_final field=$field5 tabindex=$i++ size="2" maxlength="2" prop="num length|2"}}
  </td>
</tr>
