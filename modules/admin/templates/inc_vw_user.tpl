<!-- $Id$ -->

<table class="form">
  <tr>
    <th class="category" colspan="2">Identité</th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <th>Nom</th>
    <td>{{$user->user_last_name}}</td>
    <th>Adresse</th>
    <td>{{$user->user_address1}}</td>
  </tr>
  
  <tr>
    <th>Prénom</th>
    <td>{{$user->user_first_name}}</td>
    <th>Code Postal</th>
    <td>{{$user->user_zip}}</td>
  </tr>
  
  <tr>
    <th>Type</th>
    <td>
      {{assign var="type" value=$user->user_type}}
      {{$utypes.$type}}
    </td>
    <th>Ville</th>
    <td>{{$user->user_city}}</td>
  </tr>
  
  <tr>
    <th>email</th>
    <td>{{$user->user_email}}</td>
    <th>Téléphone</th>
    <td>{{$user->user_phone}}</td>
  </tr>
</table>