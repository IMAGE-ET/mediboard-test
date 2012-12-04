{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import d'utilisateurs Mediboard.</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont séparés par
  <strong>;</strong> et les textes par <strong>"</strong>, la première ligne étant ignorée :
  <ul>
    <li>Nom *</li>
    <li>Prénom *</li>
    <li>Login</li>
    <li>Mot de passe</li>
    <li>Type (code numérique) *</li>
    <li>Fonction (nom) *</li>
    <li>Profil (username)</li>
  </ul>
  <em>* : champs obligatoires</em>
</div>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />
  
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
<table class="tbl">
  <tr>
    <th class="title" colspan="8">{{$results|@count}} utilisateurs trouvés</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>Nom</th>
    <th>Prénom</th>
    <th>Nom d'utilisateur</th>
    <th>Mot de passe</th>
    <th>Type</th>
    <th>Fonction</th>
    <th>Profil</th>
  </tr>
  {{foreach from=$results item=_user}}
  <tr>
    <td class="text">
      {{if $_user.error}}
        {{$_user.error}}
      {{else}}
        OK
      {{/if}}
    </td>
    <td>{{$_user.lastname}}</td>
    <td>{{$_user.firstname}}</td>
    <td>{{$_user.username}}</td>
    <td>{{$_user.password}}</td>
    <td>{{$_user.type}}</td>
    <td>{{$_user.function_name}}</td>
    <td>{{$_user.profil_name}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

