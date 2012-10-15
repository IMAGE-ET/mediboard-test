{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import de salles de bloc Mediboard.</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont séparés par
  <strong>;</strong> et les textes par <strong>"</strong>, la première ligne étant ignorée :
  <ul>
    <li>Nom du bloc *</li>
    <li>Nom de la salle *</li>
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
    <th class="title" colspan="3">{{$results|@count}} salles trouvés</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>Bloc</th>
    <th>Salle</th>
  </tr>
  {{foreach from=$results item=_salle}}
  <tr>
    <td class="text">
      {{if $_salle.error}}
        {{$_salle.error}}
      {{else}}
        OK
      {{/if}}
    </td>
    <td>{{$_salle.bloc}}</td>
    <td>{{$_salle.nom}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

