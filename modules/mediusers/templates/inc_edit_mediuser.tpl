<script type="text/javascript">
  
{{if $object->_id}}
Main.add(function () {
  showPratInfo("{{$object->_user_type}}");
  loadProfil("{{$object->_user_type}}");
  
  var form = getForm("mediuser");
  if ($(form.spec_cpam_id)) {
    form.spec_cpam_id.makeAutocomplete({width: "200px"});
  }
});
{{/if}}

</script>

{{if $object->_id}}
<a class="button search" style="" href="?m=admin&amp;tab=view_edit_users&amp;user_username={{$object->_user_username}}&amp;user_id={{$object->_id}}">
  Administrer cet utilisateur
</a>
{{/if}}
<form name="mediuser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

<input type="hidden" name="dosql" value="do_mediusers_aed" />
<input type="hidden" name="user_id" value="{{$object->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $object->_id}}
    <th class="title modify text" colspan="2">
    
      {{mb_include module=system template=inc_object_idsante400 object=$object}}
      {{mb_include module=system template=inc_object_history object=$object}}
      
{{tr}}CMediusers-title-modify{{/tr}} 
'{{$object->_user_username}}'
    </th>

    {{else}}
    <th class="title" colspan="2">
      {{tr}}CMediusers-title-create{{/tr}}
    </th>
    {{/if}}
  </tr>

  <tr>
    <th>{{mb_label object=$object field="_user_username"}}</th>
    <td>{{mb_field object=$object field="_user_username"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="_user_password"}}</th>
    <td>
     <input type="password" name="_user_password" class="{{$object->_props._user_password}}{{if !$object->user_id}} notNull{{/if}}" onkeyup="checkFormElement(this);" value="" />
     <span id="mediuser__user_password_message"></span>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="_user_password2"}}</th>
    <td><input type="password" name="_user_password2" class="password sameAs|_user_password" value="" /></td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="actif"}}</th>
    <td>{{mb_field object=$object field="actif"}}</td>
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
        <option value="">&mdash; Choisir une fonction</option>
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
        <option value="">&mdash; Choisir un profil</option>
        {{foreach from=$profiles item=curr_profile}}
        <option value="{{$curr_profile->user_id}}" {{if $curr_profile->user_id == $object->_profile_id}} selected="selected" {{/if}}>{{$curr_profile->user_username}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="_user_last_name"}}</th>
    <td>{{mb_field object=$object field="_user_last_name"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="_user_first_name"}}</th>
    <td>{{mb_field object=$object field="_user_first_name"}}</td>
  </tr>
          
  <tr>
    <th>{{mb_label object=$object field="_user_email"}}</th>
    <td>{{mb_field object=$object field="_user_email"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="_user_phone"}}</th>
    <td>{{mb_field object=$object field="_user_phone"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="commentaires"}}</th>
    <td>{{mb_field object=$object field="commentaires"}}</td>
  </tr>
  
  <tbody id="show_prat_info" style="display:none">
  
    {{include file="inc_infos_praticien.tpl"}}
             
  </tbody>
  
  <tr>
    <td class="button" colspan="2">
      {{if $object->user_id}}
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur',objName:'{{$object->_user_username|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
      {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>
</form>