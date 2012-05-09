{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Ajout d'interventions dans Mediboard par CSV </h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont séparés par
  <strong>;</strong> et les textes par <strong>"</strong> :
  <ul>
    <li>NDA *</li>
    <li>ADELI *</li>
    <li>DATE/HEURE DEBUT *</li>
    <li>DATE/HEURE FIN *</li>
    <li>LIBELLE *</li>
    <li>SALLE </li>
    <li>COTE (droit/gauche/bilateral/total/inconnu)</li>
  </ul>
  <em>* : champs obligatoires</em>
</div>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />
  
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="3">{{$results|@count}} interventions </th>
    </tr>
    <tr>
      <th>NDA</th>
      <th>Adeli</th>
      <th>Etat</th>
    </tr>
    {{foreach from=$results item=_result}}
    <tr>
      <td class="narrow">{{$_result.NDA}}</td>
      <td class="narrow">{{$_result.ADELI}}</td>
      <td class="text {{if !$_result.error}}ok{{else}}error{{/if}}">
        {{if $_result.error}}
          {{$_result.error}}
        {{/if}}
      </td>
    </tr>
    {{/foreach}}
  </table>
{{/if}}