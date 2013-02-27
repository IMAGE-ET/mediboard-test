<tr>
  <th>{{mb_label object=$object field="_user_username"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="_user_username"}}
    {{else}}
      {{mb_value object=$object field="_user_username"}}
      {{mb_field object=$object field="_user_username" hidden=true}}
    {{/if}}
  </td>
</tr>

{{if !$readOnlyLDAP}}
<tr>
  <th>{{mb_label object=$object field="_user_password"}}</th>
  <td>
    <input type="password" name="_user_password"
          class="{{$object->_props._user_password}}{{if !$object->user_id}} notNull{{/if}}"
          onkeyup="checkFormElement(this);" value="" />
    <span id="mediuser__user_password_message"></span>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_password2"}}</th>
  <td>
    <input
      type="password"
      name="_user_password2"
      value=""
      class="{{$object->_props._user_password2}}{{if !$object->user_id}} notNull{{/if}}"
    />
  </td>
</tr>

{{/if}}
<tr>
  <th>{{mb_label object=$object field="actif"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="actif"}}
    {{else}}
      {{mb_value object=$object field="actif"}}
      {{mb_field object=$object field="actif" hidden=true}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="deb_activite"}}</th>
  <td>{{mb_field object=$object field="deb_activite" form="mediuser" register=true}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="fin_activite"}}</th>
  <td>{{mb_field object=$object field="fin_activite" form="mediuser" register=true}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="remote"}}</th>
  <td>{{mb_field object=$object field="remote" onchange="changeRemote(this)"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="function_id"}}</th>
  <td>
    <select name="function_id" style="width: 150px;" class="{{$object->_props.function_id}}">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$group->_ref_functions item=_function}}
      <option class="mediuser" style="border-color: #{{$_function->color}};" value="{{$_function->_id}}"
         {{if $_function->_id == $object->function_id}} selected="selected" {{/if}}
      >
        {{$_function->text}}
      </option>
      {{foreachelse}}
      <option value="" disabled="disabled">
        {{tr}}CFunctions.none{{/tr}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_type"}}</th>
  <td>
    <select name="_user_type"  style="width: 150px;" class="{{$object->_props._user_type}}" onchange="showPratInfo(this.value); loadProfil(this.value)">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$utypes key=curr_key item=type}}
      <option value="{{if $curr_key != 0}}{{$curr_key}}{{/if}}" {{if $curr_key == $object->_user_type}}selected="selected"{{/if}}>{{$type}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_profile_id"}}</th>
  <td>
    <select name="_profile_id" style="width: 150px;">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$profiles item=_profile}}
      <option value="{{$_profile->user_id}}" {{if $_profile->user_id == $object->_profile_id}} selected="selected" {{/if}}>{{$_profile->user_username}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_last_name"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="_user_last_name"}}
    {{else}}
      {{mb_value object=$object field="_user_last_name"}}
      {{mb_field object=$object field="_user_last_name" hidden=true}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_first_name"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="_user_first_name"}}
    {{else}}
      {{mb_value object=$object field="_user_first_name"}}
      {{mb_field object=$object field="_user_first_name" hidden=true}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="initials"}}</th>
  <td>
    {{mb_field object=$object field="initials"}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_email"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="_user_email"}}
    {{else}}
      {{mb_value object=$object field="_user_email"}}
      {{mb_field object=$object field="_user_email" hidden=true}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_phone"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="_user_phone"}}
    {{else}}
      {{mb_value object=$object field="_user_phone"}}
      {{mb_field object=$object field="_user_phone" hidden=true}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="_user_astreinte"}}</th>
  <td>
    {{if !$readOnlyLDAP}}
      {{mb_field object=$object field="_user_astreinte"}}
    {{else}}
      {{mb_value object=$object field="_user_astreinte"}}
      {{mb_field object=$object field="_user_astreinte" hidden=true}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="commentaires"}}</th>
  <td>{{mb_field object=$object field="commentaires"}}</td>
</tr>
