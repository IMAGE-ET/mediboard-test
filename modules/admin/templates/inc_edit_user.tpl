<!-- $Id: $ -->

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_user_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />

<table class="form">
  <tr>
    {{if $user->_id}}
    <th class="title modify" colspan="2">
      Utilisateur : {{$user->_view}}
    {{else}}
    <th class="title" colspan="2">
      Création d'utilisateur
    {{/if}}
    </th>
  </tr>


  <tr>
    <th class="category" colspan="2">Informations de connexion</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_username"}}</th>
    <td>{{mb_field tabindex="101" object=$user field="user_username"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="template"}}</th>
    <td>{{mb_field tabindex="102" object=$user field="template"}}</td>
  </tr>
  <tr>
    <th><label for="_user_password" title="Saisir le mot de passe. Obligatoire">Mot de passe</label></th>
    <td><input tabindex="103" type="password" name="_user_password" class="{{$specs._user_password}}{{if !$user->user_id}} notNull{{/if}}" value="" onkeyup="checkFormElement(this)" />
		<span id="editFrm__user_password_message"></span>
    </td>
  </tr>
  <tr>
    <th><label for="_user_password2" title="Re-saisir le mot de passe pour confimer. Obligatoire">Mot de passe (bis)</label></th>
    <td><input tabindex="104" type="password" name="_user_password2" class="password sameAs|_user_password" value="" /></td>
  </tr>


  <tr>
    <th class="category" colspan="2">Identité</th>
  </tr>

  <tr>
    <th>{{mb_label object=$user field="user_last_name" }}</th>
    <td>{{mb_field tabindex="105" object=$user field="user_last_name"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_first_name"}}</th>
    <td>{{mb_field tabindex="106" object=$user field="user_first_name"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_type"}}</th>
    <td>
      <select tabindex="107" name="user_type" class="{{$user->_props.user_type}}">
        {{foreach from=$utypes|smarty:nodefaults key=curr_key item=type}}
        <option value="{{$curr_key}}" {{if $curr_key == $user->user_type}}selected="selected"{{/if}}>{{$type}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="user_email"}}</th>
    <td>{{mb_field tabindex="108" object=$user field="user_email" size="20"}}</td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_address1"}}</th>
    <td>{{mb_field tabindex="109" object=$user field="user_address1"}}</td>
  </tr>
  

  <tr>
    <th>{{mb_label object=$user field="user_zip"}}</th>
    <td>{{mb_field tabindex="110" object=$user field="user_zip"}}</td>
  </tr>
  

  <tr>
    <th>{{mb_label object=$user field="user_city"}}</th>
    <td>{{mb_field tabindex="111" object=$user field="user_city" size="20"}}</td>
  </tr>
  

  <tr>
    <th>{{mb_label object=$user field="user_phone"}}</th>
    <td>{{mb_field tabindex="112" object=$user field="user_phone" size="20"}}</td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      {{if $user->user_id}}
      <button class="modify" type="submit">Valider</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {
      	  typeName:'l\'utilisateur',
      	  objName:'{{$user->user_username|smarty:nodefaults|JSAttribute}}'
        })">
        Supprimer
      </button>
      {{else}}
      <button class="submit" type="submit">Créer</button>
      {{/if}}
    </td>
  </tr>
</table>

</form>