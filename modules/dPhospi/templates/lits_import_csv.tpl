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
  Veuillez indiquez les champs suivants dans un fichier CSV dont les champs sont séparés par
  <strong>;</strong> et les textes par <strong>"</strong>, la première ligne étant sautée :
  <ul>
    <li>Nom du service *</li>
    <li>Nom de la chambre *</li>
    <li>Nom du lit *</li>
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
    <th class="title" colspan="3">{{$results|@count}} lits trouvés</th>
  </tr>
  <tr>
    <th>Service</th>
    <th>Chambre</th>
    <th>Lit</th>
  </tr>
  {{foreach from=$results item=_lit}}
  <tr>
    <td>{{$_lit.service}}</td>
    <td>{{$_lit.chambre}}</td>
    <td>{{$_lit.nom}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

