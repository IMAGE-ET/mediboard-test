<!-- $Id: inc_vw_patient.tpl 701 2006-09-01 11:23:17Z maskas $ -->

<form name="editFrm" action="index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_user_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />

<table class="form">
  <tr>
    <th class="title" colspan="4">
      {{if $user->user_id}}
      Utilisateur : {{$user->_view}}
      {{else}}
      Création d'utilisateur
      {{/if}}
    </th>
  </tr>


  <tr>
    <th class="category" colspan="4">Informations de connexion</th>
  </tr>

  <tr>
    <th rowspan="2"><label for="user_username" title="Veuillez choisir un nom d'utilisateur">Nom d'utilisateur</label></th>
    <td rowspan="2"><input tabindex="101" type="text" name="user_username" value="{{$user->user_username}}" title="{{$user->_props.user_username}}" /></td>
    <th><label for="user_password" title="Veuillez choisir un mot de passe">Mot de passe</label></th>
    <td><input tabindex="102" type="password" name="user_password" value="{{$user->user_password}}" title="{{$user->_props.user_password}}"/></td>
  </tr>
  <tr>
    <th><label for="_user_password" title="Veuillez saisir une nouvelle fois votre mot de passe">Mot de passe (vérif.)</label></th>
    <td><input tabindex="103" type="password" name="_user_password" value="{{$user->user_password}}" title="{{$user->_props.user_password}}|sameAs|user_password"/></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Identité</th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <th><label for="user_last_name" title="Veuillez saisir votre nom">Nom</label></th>
    <td><input tabindex="104" type="text" name="user_last_name" value="{{$user->user_last_name}}" title="{{$user->_props.user_last_name}}" /></td>
    <th><label for="user_address1" title="Veuillez saisir votre adresse">Adresse</label></th>
    <td><input tabindex="108" type="text" name="user_address1" value="{{$user->user_address1}}" title="{{$user->_props.user_address1}}" /></td>
  </tr>
  
  <tr>
    <th><label for="user_first_name" title="Veuillez saisir votre prénom">Prénom</label></th>
    <td><input tabindex="105" type="text" name="user_first_name" value="{{$user->user_first_name}}" title="{{$user->_props.user_first_name}}" /></td>
    <th><label for="user_zip" title="Veuillez saisir voter code postal">Code Postal</label></th>
    <td><input tabindex="109" type="text" name="user_zip" value="{{$user->user_zip}}" title="{{$user->_props.user_zip}}" /></td>
  </tr>
  
  <tr>
    <th><label for="user_type" title="Veuillez choisir un type d'utilisateur">Type</label></th>
    <td>
      <select tabindex="106" name="user_type" title="{{$user->_props.user_type}}">
        {{foreach from=$utypes|smarty:nodefaults key=curr_key item=type}}
        <option value="{{$curr_key}}" {{if $curr_key == $user->user_type}}selected="selected"{{/if}}>{{$type}}</option>
        {{/foreach}}
      </select>
    </td>
    <th><label for="user_city" title="Veuillez saisir votre ville">Ville</label></th>
    <td><input tabindex="110" type="text" name="user_city" value="{{$user->user_city}}" title="{{$user->_props.user_city}}" /></td>
  </tr>
  
  <tr>
    <th><label for="user_email" title="Veuillez saisir notre adresse e-mail">email</label></th>
    <td><input tabindex="107" type="text" name="user_email" value="{{$user->user_email}}" title="{{$user->_props.user_email}}" /></td>
    <th><label for="user_phone" title="Veuillez saisir votre numéro de téléphone">Téléphone</label></th>
    <td><input tabindex="111" type="text" name="user_phone" value="{{$user->user_phone}}" title="{{$user->_props.user_phone}}" /></td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="submit" type="submit">Sauver</button>
    </td>
  </tr>
</table>

</form>