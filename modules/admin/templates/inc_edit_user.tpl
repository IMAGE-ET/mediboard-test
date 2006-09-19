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
    <th rowspan="2">Nom d'utilisateur</th>
    <td rowspan="2"><input tabindex="101" type="text" name="user_username" value="{{$user->user_username}}" /></td>
    <th>Mot de passe</th>
    <td><input tabindex="102" type="password" name="user_password" value="{{$user->user_password}}" /></td>
  </tr>
  <tr>
    <th>Mot de passe (vérif.)</th>
    <td><input tabindex="103" type="password" name="_user_password" value="{{$user->user_password}}" /></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Identité</th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <th>Nom</th>
    <td><input tabindex="104" type="text" name="user_last_name" value="{{$user->user_last_name}}" /></td>
    <th>Adresse</th>
    <td><input tabindex="108" type="text" name="user_address1" value="{{$user->user_address1}}" /></td>
  </tr>
  
  <tr>
    <th>Prénom</th>
    <td><input tabindex="105" type="text" name="user_first_name" value="{{$user->user_first_name}}" /></td>
    <th>Code Postal</th>
    <td><input tabindex="109" type="text" name="user_zip" value="{{$user->user_zip}}" /></td>
  </tr>
  
  <tr>
    <th>Type</th>
    <td>
      <select tabindex="106" name="user_type">
        {{foreach from=$utypes key=curr_key item=type}}
        <option value="{{$curr_key}}" {{if $curr_key == $user->user_type}}selected="selected"{{/if}}>{{$type}}</option>
        {{/foreach}}
      </select>
    </td>
    <th>Ville</th>
    <td><input tabindex="110" type="text" name="user_city" value="{{$user->user_city}}" /></td>
  </tr>
  
  <tr>
    <th>email</th>
    <td><input tabindex="107" type="text" name="user_email" value="{{$user->user_email}}" /></td>
    <th>Téléphone</th>
    <td><input tabindex="111" type="text" name="user_phone" value="{{$user->user_phone}}" /></td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="save" type="submit">Sauver</button>
    </td>
  </tr>
</table>

</form>