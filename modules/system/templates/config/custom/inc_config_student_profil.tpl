{{if $is_last}}
  <select name="c[{{$_feature}}]" onchange="" {{if $is_inherited}} disabled="disabled" {{/if}}>
    {{foreach from="CUser::getProfiles"|static_call:null key=profile_id item=_profile}}
      <option value="{{$profile_id}}" {{if $profile_id == $value}}selected="selected"{{/if}}>
        {{$_profile->user_last_name}}
      </option>
    {{/foreach}}
  </select>
{{else}}
  {{if $value}}
    {{$value}}
  {{/if}}
{{/if}}